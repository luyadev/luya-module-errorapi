<?php

namespace luya\errorapi\adapters;

use Sentry;
use luya\errorapi\BaseIntegrationAdapter;
use luya\errorapi\models\Data;
use luya\helpers\Inflector;
use Curl\Curl;
use Sentry\State\Scope;
use yii\helpers\Html;
use luya\Exception;
use yii\helpers\Json;

class SentryAdapter extends BaseIntegrationAdapter
{
    public $token;
    public $organisation;
    public $team;

    /**
     * Undocumented function
     * 
     * @see https://docs.sentry.io/development/sdk-dev/interfaces/#stack-trace-interface
     *
     * @param Data $data
     * @return void
     */
    private function generateStackTraceFrames(Data $data)
    {
        $frames = [];
        foreach ($data->getTrace() as $trace) {
            $frames[] = [
                'filename' => $trace->file,
                'function' => $trace->function,
                'lineno' => $trace->line,
                'module' => $trace->class,
            ];
        }

        return $frames;
    }

    private function genearteContexts(Data $data)
    {
        $contexts = [];
        // os
        if ($data->getWhichBrowser()) {
            $contexts['os'] = [
                'version' => $data->getWhichBrowser()->os->version->value,
                'name' => $data->getWhichBrowser()->os->name,
                'type' => 'os',
            ];
        }

        // browser
        if ($data->getWhichBrowser()->browser->name) {
            $contexts['browser'] = [
                'version' => $data->getWhichBrowser()->browser->version->value,
                'name' => $data->getWhichBrowser()->browser->name,
                'type' => 'browser',
            ];
        }

        return $contexts;
    }

    /**
     * Undocumented function
     *
     * @see https://docs.sentry.io/development/sdk-dev/attributes/
     * @param Data $data
     * @return void
     */
    private function generateEventArray(Data $data)
    {
        /*"contexts":{  
      "runtime":{  
         "version":"7.2.10",
         "type":"runtime",
         "name":"php"
      }
   },
   */
        return array_filter([
            'transaction' => $data->getFile(),
            'server_name' => $data->getServerName(),
            'metadata' => [
                'value' => $data->getErrorMessage(),
                'filename' => $data->getFile(),
            ],
            'fingerprint' => [
                $data->getRequestUri(),
                $data->getServerName(),
            ],
            'logger' => 'luya.errorapi',
            'platform' => 'php',
            'sdk' => [
                'name' => 'luya-errorapi',
                'version' => '1.0.0',
            ],
            'environment' => 'prod',
            'level' => 'error',
            'contexts' => $this->genearteContexts($data),
            'tags' => [
                'luya_version' => '1.0',
                'file' => $data->getFile(),
                'url' => $data->getServer('SCRIPT_URI'),
            ],
            'user' => [
                'ip_address' => $data->getIp(),
            ],
            'extra' => [
                'request_uri' => $data->getRequestUri(),
                'line' => $data->getLine(),
                'post' => $data->getPost(),
                'get' => $data->getGet(),
                'server' => $data->getServer(),
                'session' => $data->getSession(),
            ],
            'exception' => [
                'values' => [
                    [
                        'type' => 'ExceptionName', // @TODO
                        'value' => $data->getErrorMessage(),
                        'stacktrace' => [
                            'frames' => $this->generateStackTraceFrames($data)
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function onCreate(Data $data)
    {
        $slug = Inflector::slug($data->getServerName());

        $auth = $this->getAuth($data, $slug);

        $url = 'https://sentry.io/api/'.$auth['id'].'/store/?sentry_version=5&sentry_key='.$auth['public'].'&sentry_secret='.$auth['secret'].'';

        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/json');
        $r = $curl->post($url, Json::encode($this->generateEventArray($data)));
        
        return $r->isSuccess();
    }

    public function getAuth(Data $data, $slug)
    {
        $curl = new Curl();
        $curl->setHeader('Authorization', 'Bearer '. $this->token);

        $hasProject = $curl->get("/api/0/projects/{$this->organisation}/{$slug}/");

        
        if (!$hasProject->isSuccess()) {
            $createProject = $curl->post("https://sentry.io/api/0/teams/{$this->organisation}/{$this->team}/projects/", [
                'name' => $data->getServerName(),
                'slug' => $slug
            ]);

        }

        // get dsn

        $keys = $curl->get("https://sentry.io/api/0/projects/{$this->organisation}/{$slug}/keys/");

        if ($keys->isError()) {
            throw new Exception("The request for organisation key went wrong, maybe invalid sentry api credentials provided?");
        }

        $dr = json_decode($keys->response, true);

        $firstKey = current($dr);

        return [
            'id' => $firstKey['projectId'],
            'public' => $firstKey['public'],
            'secret' => $firstKey['secret'],
        ];
    }
}

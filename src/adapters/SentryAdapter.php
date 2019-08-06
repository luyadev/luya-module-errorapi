<?php

namespace luya\errorapi\adapters;

use luya\errorapi\BaseIntegrationAdapter;
use luya\errorapi\models\Data;
use luya\helpers\Inflector;
use Curl\Curl;
use luya\Exception;
use yii\helpers\Json;
use yii\base\InvalidConfigException;
use luya\helpers\Url;

/**
 * Sentry Integration.
 * 
 * @since 2.0.0
 * @author Basil Suter <basil@nadar.io>
 */
class SentryAdapter extends BaseIntegrationAdapter
{
    /**
     * @var string The sentry API token for your Organisation.
     */
    public $token;

    /**
     * @var string The organisation name in where the projects will be stored.
     */
    public $organisation;

    /**
     * @var string The team slug on which behalf the projects will be created.
     */
    public $team;

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();

        if (!$this->token || !$this->organisation || !$this->team) {
            throw new InvalidConfigException("The sentry adapter token, team and organisation property can not be empty.");
        }
    }

    /**
     * {@inheritDoc}
     */
    public function onCreate(Data $data)
    {
        $auth = $this->getAuth($data);
        $url = 'https://sentry.io/api/'.$auth['id'].'/store/?sentry_version=5&sentry_key='.$auth['public'].'&sentry_secret='.$auth['secret'].'';
        $curl = new Curl();
        $curl->setHeader('Content-Type', 'application/json');
        return $curl->post($url, Json::encode($this->generateStorePayload($data)))->isSuccess();
    }

    /**
     * Generate the project slug from the given data server name.
     *
     * @param Data $data
     * @return stirng
     * @since 2.0.1
     */
    public function generateProjectSlug(Data $data)
    {
        $serverName = Url::domain($data->getServerName());
        return Inflector::slug(Inflector::camel2words($serverName));
    }

    /**
     * Generate an array containing auth informations
     *
     * + id:
     * + public:
     * + secret:
     * 
     * @param Data $data
     * @return array
     * @throws Exception If an error while accessing the api appears, an exception is thrown.
     */
    public function getAuth(Data $data)
    {
        $slug = $this->generateProjectSlug($data);
        $curl = new Curl();
        $curl->setHeader('Authorization', 'Bearer '. $this->token);
        $hasProject = $curl->get("/api/0/projects/{$this->organisation}/{$slug}/");

        // create the project if not exists
        if (!$hasProject->isSuccess()) {
            $curl->post("https://sentry.io/api/0/teams/{$this->organisation}/{$this->team}/projects/", [
                'name' => Url::domain($data->getServerName()),
                'slug' => $slug
            ]);
        }

        // get project id with credentials

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

    /**
     * Generate Stack Trace Frames.
     * 
     * @see https://docs.sentry.io/development/sdk-dev/interfaces/#stack-trace-interface
     * @param Data $data
     * @return array
     */
    public function generateStackTraceFrames(Data $data)
    {
        $frames = [];
        foreach ($data->getTrace() as $trace) {
            $frames[] = [
                'filename' => $trace->file,
                'function' => $trace->function,
                'lineno' => $trace->line,
                //'module' => $trace->class,
                'context_line' => $trace->context_line,
                'pre_context' => $trace->pre_context,
                'post_context' => $trace->post_context,
                'abs_path' => $trace->abs_path,
            ];
        }

        return $frames;
    }

    /**
     * Generate Context Informations.
     *
     * @param Data $data
     * @return array
     */
    public function generateContext(Data $data)
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

        if ($data->getPhpVersion()) {
            $contexts['runtime'] = [
                'version' => $data->getPhpVersion(),
                'type' => 'runtime',
                'name' => 'php',
            ];
        }

        return $contexts;
    }

    /**
     * Genereate the store payload
     *
     * @see https://docs.sentry.io/development/sdk-dev/attributes/
     * @param Data $data
     * @return array
     */
    public function generateStorePayload(Data $data)
    {
        return array_filter([
            'transaction' => $data->getFile(),
            'server_name' => $data->getServerName(),
            'metadata' => [
                'value' => $data->getErrorMessage(),
                'filename' => $data->getFile(),
            ],
            'fingerprint' => [
                // '{{ default }}', // https://docs.sentry.io/data-management/rollups/,
                $data->getErrorMessage(),
                $data->getRequestUri(),
            ],
            'logger' => 'luya.errorapi',
            'platform' => 'php',
            'sdk' => [
                'name' => 'luya-errorapi',
                'version' => '2.0.0',
            ],
            'environment' => $data->getYiiEnv(),
            'level' => 'error',
            'contexts' => $this->generateContext($data),
            'tags' => [
                'luya_version' => $data->getLuyaVersion(),
                'php_version' => $data->getPhpVersion(),
                'file' => $data->getFile(),
                'url' => $data->getServer('SCRIPT_URI'),
                // 'domain' => Url::domain($data->getServer('SCRIPT_URI')), // after luya cor release 1.0.20
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
                'yii_debug' => $data->getYiiDebug(),
                'yii_env' => $data->getYiiEnv(),
                'http_status_code' => $data->getStatusCode(),
                'exception_name' => $data->getExceptionName(),
            ],
            'exception' => [
                'values' => [
                    [
                        'type' => $data->getExceptionClassName(),
                        'value' => $data->getErrorMessage(),
                        'stacktrace' => [
                            'frames' => $this->generateStackTraceFrames($data)
                        ]
                    ]
                ]
            ]
        ]);
    }
}

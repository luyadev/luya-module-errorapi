<?php

namespace luya\errorapi\adapters;

use Sentry;
use luya\errorapi\BaseIntegrationAdapter;
use luya\errorapi\models\Data;
use luya\helpers\Inflector;
use Curl\Curl;
use Sentry\State\Scope;
use yii\helpers\Html;

class SentryAdapter extends BaseIntegrationAdapter
{
    public $token;
    public $organisation;
    public $team;

    public function onCreate(Data $data)
    {
        $slug = Inflector::slug($data->getServerName());

        $dsn = $this->getProjectDsn($data, $slug);
        
       
        Sentry\init(['dsn' => $dsn]);
        
        Sentry\configureScope(function (Scope $scope) use($data): void {

            $this->setTag($scope, 'user', $data->getIp());
            $this->setTag($scope, 'user', $data->getIp());
            $this->setTag($scope, 'server_name', $data->getServerName());
            $this->setTag($scope, 'line', $data->getLine());
            $this->setTag($scope, 'file', $data->getFile());
            $this->setTag($scope, 'request_uri', $data->getRequestUri());


            // TRACES
            // @see scope trace? see: https://github.com/olegtsvetkov/yii2-sentry/blob/master/src/LogTarget.php#L60
            $traces = [];
            foreach ($data->getTrace() as $trace) {
                $traces[] = "in {$trace->file}:{$trace->line}";            }

            if (!empty($traces)) {
                $scope->setExtra('traces', $traces);
            }

            // configure fingerprint identifier
            $scope->setFingerprint([
                $data->getErrorMessage(),
                $data->getServerName(),
            ]);

            foreach ($data->getErrorArray() as $k => $v) {
                if (is_scalar($v)) {
                    $scope->setExtra($k, $v);
                }
            }

        });
        $r = Sentry\captureMessage($data->getErrorMessage());

        return (bool) $r;
    }

    private function setTag(Scope $scope, $key, $value)
    {
        if (!empty($value)) {
            $scope->setTag($key, Html::encode($value));
        }
    }

    public function getProjectDsn(Data $data, $slug)
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
        $dr = json_decode($keys->response, true);

        $firstKey = current($dr);

        return $firstKey['dsn']['public'];
    }
}

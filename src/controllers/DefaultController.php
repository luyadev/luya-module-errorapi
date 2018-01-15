<?php

namespace luya\errorapi\controllers;

use Yii;
use luya\errorapi\models\Data;

/**
 * Default Controller for the Error API.
 *
 * The `create` action is used in order to recieve error reports from a given website.
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class DefaultController extends \luya\rest\Controller
{
    /**
     * @inheritdoc
     */
    public function userAuthClass()
    {
        return false;
    }

    /**
     * Create a new error report.
     *
     * In order to create new error report send a post request with the key `error_json` containing a json.
     *
     * Example:
     *
     * ```php
     * $_POST['error_json'] = json_encode(['message' => 'What?', 'serverName' => 'example.com']);
     * ```
     */
    public function actionCreate()
    {
        $model = new Data();
        $model->error_json = Yii::$app->request->post('error_json', null);
        
        if ($model->save()) {
            // send slack message if enabled
            if ($this->module->slackToken) {
                $this->sendSlackMessage('#'.$model->identifier.' | '.$model->getServerName().': '.$model->getErrorMessage(), $this->module->slackChannel);
            }
            // send error email if recipients are provided.
            if (!empty($this->module->recipient)) {
                Yii::$app->mail
                    ->compose($model->serverName . ' Error', $this->renderMail($model))
                    ->addresses($this->module->recipient)
                    ->send();
            }
            
            return true;
        }
        
        return $this->sendModelError($model);
    }
    
    /**
     * Render the error EMail.
     * 
     * @param Data $model
     * @return string
     */
    protected function renderMail(Data $model)
    {
        return $this->renderPartial('_mail', [
            'model' => $model,
            'issueLink' => $model->getIssueLink($this->module->issueCreateRepo),
        ]);
    }

    /**
     * Send a message to given slack channel.
     *
     * @param string $message The message to be sent.
     * @param string $channel The channel where the message should appear.
     * @return mixed
     */
    protected function sendSlackMessage($message, $channel)
    {
        $ch = curl_init('https://slack.com/api/chat.postMessage');
        $data = http_build_query([
            'token' => $this->module->slackToken,
            'channel' => $channel,
            'text' => $message,
            'username' => 'Exceptions',
        ]);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}

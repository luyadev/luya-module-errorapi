<?php

namespace luya\errorapi\adapters;

use luya\errorapi\BaseIntegrationAdapter;
use luya\errorapi\models\Data;
use yii\base\InvalidConfigException;

/**
 * Slack Adapter.
 * 
 * In order to generate a token:
 * 
 * 1.) Create an APP -> https://api.slack.com/apps/
 * 2.) See menu entry "Install App"
 * 3.) Use the "Bot User OAuth Token"
 * 
 * @author Basil Suter <basil@nadar.io>
 * @since 2.0.0
 */
class SlackAdapter extends BaseIntegrationAdapter
{
    /**
     * @var string The channel to post the slack message. Default is #luya
     */
    public $channel = '#luya';

    /**
     * @var string Slack auth token known as "Bot User OAuth Token", starting with `xoxb-...`
     */
    public $token;

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();

        if (empty($this->token)) {
            throw new InvalidConfigException("The slack adapter token property can not be empty.");
        }
    }

    /**
     * {@inheritDoc}
     */
    public function onCreate(Data $data)
    {
        $message = $this->generateSlackMessage($data);
        $this->sendSlackMessage($message, $this->channel, $this->token);
    }

    /**
     * Generate a slack message for the current Data model.
     * 
     * @param Data $model
     * @return string
     * @since 1.0.2
     */
    public function generateSlackMessage(Data $model)
    {
        $infos = [
            'ServerName' => $model->getServerName(),
            'requestUri' => $model->getErrorArrayKey('requestUri'),
            'Message' => $model->getErrorMessage(),
            'File' => $model->getErrorArrayKey('file'),
        ];

        $msg = [];
        foreach (array_filter($infos) as $key => $value) {
            $msg[] = $key . ': ' . $value;
        }
        return implode(PHP_EOL, $msg);
    }

    /**
     * Send a message to given slack channel.
     *
     * @param string $message The message to be sent.
     * @param string $channel The channel where the message should appear.
     * @return mixed
     */
    public function sendSlackMessage($message, $channel, $token)
    {
        $ch = curl_init('https://slack.com/api/chat.postMessage');
        $data = http_build_query([
            'token' => $token,
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
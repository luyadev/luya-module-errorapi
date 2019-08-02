<?php

namespace luya\errorapi\adapters;

use Yii;
use luya\errorapi\BaseIntegrationAdapter;
use luya\errorapi\models\Data;
use yii\base\InvalidConfigException;

/**
 * E-Mail integration
 * 
 * @since 2.0.0
 * @author Basil Suter <basil@nadar.io>
 */
class MailAdapter extends BaseIntegrationAdapter
{
    public $recipient = [];

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        parent::init();

        if (empty($this->recipient)) {
            throw new InvalidConfigException("The mail adapter recipient property can not be empty.");
        }
    }

    /**
     * {@inheritDoc}
     */
    public function onCreate(Data $data)
    {
        return Yii::$app->mail
            ->compose($data->getServerName() . ' Error', $this->renderMail($data))
            ->addresses((array) $this->recipient)
            ->send();    
    }
    
    /**
     * Render the error EMail.
     *
     * @param Data $model
     * @return string
     */
    public function renderMail(Data $model)
    {
        return Yii::$app->view->render('@errorapi/views/adapter/mail', [
            'model' => $model,
            'issueLink' => $model->getIssueLink($this->getModule()->issueCreateRepo),
        ]);
    }
}
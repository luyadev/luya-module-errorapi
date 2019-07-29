<?php

namespace luya\errorapi\adapters;

use Yii;
use luya\errorapi\BaseIntegrationAdapter;
use luya\errorapi\models\Data;

class MailAdapter extends BaseIntegrationAdapter
{
    public $recipient = [];

    public function onCreate(Data $data)
    {
        Yii::$app->mail
            ->compose($data->getServerName() . ' Error', $this->renderMail($data))
            ->addresses($this->recipient)
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
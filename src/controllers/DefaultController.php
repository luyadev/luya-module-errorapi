<?php

namespace luya\errorapi\controllers;

use Yii;
use luya\errorapi\models\Data;
use luya\errorapi\BaseIntegrationAdapter;

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

            foreach ($this->module->adapters as $adapterConfig) {
                /** @var BaseIntegrationAdapter $object */
                $object = Yii::createObject($adapterConfig);
                $object->run($model, $this->module);
            }
            
            return true;
        }
        
        return $this->sendModelError($model);
    }
}

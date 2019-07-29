<?php

namespace luya\errorapi;

use luya\errorapi\models\Data;
use yii\base\BaseObject;

abstract class BaseIntegrationAdapter extends BaseObject
{
    private $_module;

    public function run(Data $data, Module $module)
    {
        $this->_module = $module;
        $this->onCreate($data);
    }

    public function getModule()
    {
        return $this->_module;
    }

    public function setModule(Module $module)
    {
        $this->_module = $module;
    }

    abstract public function onCreate(Data $data);
}
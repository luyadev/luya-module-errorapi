<?php

namespace luya\errorapi;

use luya\errorapi\models\Data;
use yii\base\BaseObject;

/**
 * Base Integration Class.
 * 
 * @property Module $module
 * 
 * @since 2.0.0
 * @author Basil Suter <basil@nadar.io>
 */
abstract class BaseIntegrationAdapter extends BaseObject
{
    /**
     * The method which will be called when the error api recieves the error data
     * and the integration object is created.
     *
     * @param Data $data
     * @return mixed
     */
    abstract public function onCreate(Data $data);

    /**
     * The run method which will be invoken by the error api controller.
     *
     * @param Data $data
     * @param Module $module
     * @return void
     */
    public function run(Data $data, Module $module)
    {
        $this->_module = $module;
        $this->onCreate($data);
    }

    private $_module;

    /**
     * Getter method for the module in order to read context.
     *
     * @return Module
     */
    public function getModule()
    {
        return $this->_module;
    }

    /**
     * Setter method for the module.
     *
     * @param Module $module
     */
    public function setModule(Module $module)
    {
        $this->_module = $module;
    }
}
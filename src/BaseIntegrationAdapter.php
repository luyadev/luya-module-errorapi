<?php

namespace luya\errorapi;

use luya\errorapi\models\Data;
use luya\helpers\StringHelper;
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
     * @var array A list of words which the servername can contain and will therefore be ignored. Example
     * 
     * ```php
     * 'invalidServers' => ['example.com']
     * ```
     * 
     * If the server name contains example.com (for example: http://example.com/foobar) the error adapter will ignore the message and
     * stops processing.
     * @since 2.2.0
     */
    public $invalidServers = [];

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

        if (!$this->isInvalidServer($data, $this->invalidServers)) {
            $this->onCreate($data);
        }
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

    /**
     * Check if the current data contains an invalid server
     *
     * @param Data $data
     * @return boolean
     * @since 2.2.0
     */
    public function isInvalidServer(Data $data, array $servers)
    {
        $serverName = $data->getServerName();

        if (empty($serverName)) {
            return true;
        } elseif (StringHelper::contains($servers, $serverName)) {
            return true;
        }
    
        return false;
    }
}
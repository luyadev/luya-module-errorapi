<?php

namespace luya\errorapi\models;

use luya\errorapi\Module;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * Error Data Model.
 *
 * @property integer $id
 * @property string $identifier
 * @property string $error_json
 * @property integer $timestamp_create
 *
 * @author Basil Suter <basil@nadar.io>
 * @since 1.0.0
 */
class Data extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'error_data';
    }
    
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        
        $this->on(self::EVENT_BEFORE_INSERT, [$this, 'eventBeforeCreate']);
    }
    
    /**
     * Before new item creation.
     *
     * @param \yii\base\Event $event
     */
    public function eventBeforeCreate($event)
    {
        if (!$this->getErrorMessage() || !$this->getServerName()) {
            $event->isValid = false;
            return $this->addError('error_json', Module::t('data_content_error'));
        }
        
        $this->timestamp_create = time();
        $this->identifier = $this->createMessageIdentifier($this->getErrorMessage());
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['error_json'], 'required'],
            [['error_json'], 'string'],
            [['timestamp_create'], 'integer'],
            [['identifier'], 'string', 'max' => 255],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'identifier' => 'Identifier',
            'error_json' => 'Error Json',
            'timestamp_create' => 'Timestamp Create',
        ];
    }
    
    private $_errorArray;
    
    /**
     * Get an array from error json.
     * 
     * @return array
     * @since 1.0.1
     */
    public function getErrorArray()
    {
        if ($this->_errorArray === null) {
            $this->_errorArray = Json::decode($this->error_json);
        }
        
        return $this->_errorArray;
    }

    /**
     * Get a sepcific key from error array.
     * 
     * @param string $key
     * @return boolean
     * @since 1.0.1
     */
    public function getErrorArrayKey($key)
    {
        return isset($this->getErrorArray()[$key]) ? $this->getErrorArray()[$key] : false;
    }
    
    /**
     * Get error message from error array.
     * 
     * @return boolean
     * @since 1.0.1
     */
    public function getErrorMessage()
    {
        return $this->getErrorArrayKey('message');
    }
    
    /**
     * Get Server name from error array.
     * @return boolean
     * @since 1.0.1
     */
    public function getServerName()
    {
        return $this->getErrorArrayKey('serverName');
    }

    /**
     * Get new issue creation header.
     *
     * @return string
     * @since 1.0.1
     */
    public function getIssueLink($server)
    {
        return $server.'/issues/new?title='.urlencode('#'. $this->identifier . ' ' . $this->getErrorMessage());
    }
    /**
     * Create identifier hash from message.
     * 
     * @param string $msg
     * @return string
     */
    public function createMessageIdentifier($msg)
    {
        return sprintf('%s', hash('crc32b', $msg));
    }
}

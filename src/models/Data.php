<?php

namespace luya\errorapi\models;

use luya\errorapi\Module;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use WhichBrowser\Parser;

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
     * @return mixed
     * @since 1.0.1
     */
    public function getErrorArrayKey($key, $default = false)
    {
        return isset($this->getErrorArray()[$key]) ? $this->getErrorArray()[$key] : $default;
    }
    
    /**
     * Get error message from error array.
     *
     * @return string
     * @since 1.0.1
     */
    public function getErrorMessage()
    {
        return $this->getErrorArrayKey('message');
    }
    
    /**
     * Get Server name from error array.
     * @return string
     * @since 1.0.1
     */
    public function getServerName()
    {
        return $this->getErrorArrayKey('serverName');
    }

    public function getFile()
    {
        return $this->getErrorArrayKey('file');
    }

    public function getLine()
    {
        return $this->getErrorArrayKey('line');
    }

    public function getRequestUri()
    {
        return $this->getErrorArrayKey('requestUri');
    }

    public function getDate()
    {
        return $this->getErrorArrayKey('date');
    }
    
    public function getIp()
    {
        return $this->getErrorArrayKey('ip');
    }

    public function getGet()
    {
        return $this->getErrorArrayKey('get', []);
    }

    public function getPost()
    {
        return $this->getErrorArrayKey('post', []);
    }
    
    public function getBodyParams()
    {
        return $this->getErrorArrayKey('bodyParams', []);
    }

    public function getSession()
    {
        return $this->getErrorArrayKey('session', []);
    }

    public function getServer($key = null)
    {
        $server = $this->getErrorArrayKey('server', []);

        if (empty($server)) {
            return false;
        }

        if ($key) {
            return isset($server[$key]) ? $server[$key] : false;
        }

        return $server;
    }

    public function getProfiling()
    {
        return $this->getErrorArrayKey('profiling', []);
    }

    public function getLogger()
    {
        return $this->getErrorArrayKey('logger', []);
    }

    /**
     * Get Which Browser
     *
     * @return Parser
     */
    public function getWhichBrowser()
    {
        return new Parser($this->getServer('HTTP_USER_AGENT'));
    }

    /**
     * Get an array with trace objects if present.
     *
     * @return Trace[]
     */
    public function getTrace()
    {
        $trace = [];
        foreach ($this->getErrorArrayKey('trace', []) as $nr => $content) {
            $trace[$nr] = new Trace([
                'file' => $content['file'],
                'line' => $content['line'],
                'function' => $content['function'],
                'class' => $content['class'],
            ]);
        }

        return $trace;
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

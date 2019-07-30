<?php

namespace luya\errorapi\tests;

use luya\testsuite\cases\WebApplicationTestCase;
use luya\testsuite\fixtures\NgRestModelFixture;
use luya\errorapi\models\Data;

class ErrorApiTestCase extends WebApplicationTestCase
{
    public function beforeSetup()
    {
        $dotenv = new \Dotenv\Dotenv(__DIR__);
        $dotenv->safeLoad();
        parent::beforeSetup();
    }
    
    public function getConfigArray()
    {
        return [
            'id' => 'errorapimodule',
            'basePath' => dirname(__DIR__),
            'language' => 'en',
            'modules' => [
                'errorapi' => [
                    'class' => 'luya\errorapi\Module',
                    'adapters' => [
                        [
                            'class' => 'luya\errorapi\adapters\MailAdapter',
                            'recipient' => ['foo@example.com'],
                        ]
                    ]
                ],
            ],
            'components' => [
                'db' => [
                    'class' => 'yii\db\Connection',
                    'dsn' => 'sqlite::memory:',
                ],
            ]
        ];
    }

    /**
     * @var NgRestModelFixture
     */
    public $fixture;

    public function afterSetup()
    {
        parent::afterSetup();

        $this->fixture = (new NgRestModelFixture([
            'modelClass' => Data::class,
            'fixtureData' => [
                'model1' => [
                    'id' => rand(2,2000),
                    'timestamp_create' => 123456789,
                    'error_json' => '{"message":"exception msg", "serverName": "https://luya.io", "requestUri": "/path/url"}',
                    'identifier' => 'abcdefgh',
                ],
            ]
        ]));
    }

    /**
     *
     * @return NgRestModelFixture
     */
    public function getDataFixture()
    {
        return $this->fixture;
    }

    public function beforeTearDown()
    {
        parent::beforeTearDown();

        if ($this->fixture) {
            $this->fixture->cleanup();
        }
    }
}

<?php

namespace luya\errorapi\tests;

use luya\testsuite\cases\WebApplicationTestCase;

class ErrorApiTestCase extends WebApplicationTestCase
{
    public function getConfigArray()
    {
        return [
            'id' => 'errorapimodule',
            'basePath' => dirname(__DIR__),
            'language' => 'en',
            'modules' => [
                'errorapi' => 'luya\errorapi\Module',
            ],
            'components' => [
                'db' => [
                    'class' => 'yii\db\Connection',
                    'dsn' => 'sqlite::memory:',
                ],
            ]
        ];
    }
}
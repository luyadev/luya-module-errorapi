<?php

namespace tests\web\luya\errorapi\models;

use luya\errorapi\tests\ErrorApiTestCase;
use luya\testsuite\fixtures\NgRestModelFixture;
use luya\errorapi\models\Data;
use luya\errorapi\controllers\DefaultController;

class DatTest extends ErrorApiTestCase
{
    public function testSlackMessage()
    {
        $model = (new NgRestModelFixture([
            'modelClass' => Data::class,
            'fixtureData' => [
                'model1' => [
                    'id' => 1,
                    'timestamp_create' => 123456789,
                    'error_json' => '{"message":"exception msg", "serverName": "https://luya.io", "requestUri": "/path/url"}',
                    'identifier' => 'abcdefgh',
                ],
            ]
        ]));

        /* @var Data $data */
        $data = $model->getModel('model1');
        
        $this->assertSame('exception msg', $data->getErrorMessage());
        $this->assertSame('https://luya.io', $data->getServerName());
        
        
        // generate slack message in controller
        $ctrl = new DefaultController('default', $this->app);
        
        $r = $this->invokeMethod($ctrl, 'generateSlackMessage', [$data]);
     
        $this->assertSameTrimmed('ID: abcdefgh ServerName: https://luya.io Time: 11/29/73 - 21:33:09 requestUri: /path/url Message: exception msg', $r);
    }
}
<?php

namespace luya\errorapi\tests\adapters;

use luya\errorapi\tests\ErrorApiTestCase;
use luya\errorapi\adapters\SlackAdapter;

class SlackAdapterTest extends ErrorApiTestCase
{
    public function testAdapter()
    {
        $model = $this->getDataFixture();

        $adapter = new SlackAdapter();
    }

    /*
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

        $data = $model->getModel('model1');
        
        $this->assertSame('exception msg', $data->getErrorMessage());
        $this->assertSame('https://luya.io', $data->getServerName());
        
        
        // generate slack message in controller
        $ctrl = new DefaultController('default', $this->app);
        
        $r = $this->invokeMethod($ctrl, 'generateSlackMessage', [$data]);
     
        $this->assertSameTrimmed('ServerName: https://luya.io requestUri: /path/url Message: exception msg', $r);
    }
    */
}
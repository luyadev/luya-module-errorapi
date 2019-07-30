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

        $message  = $adapter->generateSlackMessage($this->getDataFixture()->getModel('model1'));

        $this->assertSameTrimmed('ServerName: https://luya.io requestUri: /path/url Message: exception msg', $message);

    }
}
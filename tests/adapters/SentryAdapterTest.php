<?php

namespace luya\errorapi\tests\adapters;

use luya\errorapi\tests\ErrorApiTestCase;
use luya\errorapi\adapters\SentryAdapter;

class SentryAdapterTest extends ErrorApiTestCase
{
    public function testAdapter()
    {
        $model = $this->getDataFixture();

        $adapter = new SentryAdapter();
        $adapter->token = getenv('sentryToken');
        $adapter->organisation = getenv('sentryOrganisation');
        $adapter->team = getenv('sentryTeam');

        $this->assertTrue($adapter->onCreate($model->getModel('model1')));
    }
}
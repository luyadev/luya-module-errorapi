<?php

namespace tests\web\luya\errorapi\models;

use luya\errorapi\tests\ErrorApiTestCase;

class DataTest extends ErrorApiTestCase
{
    public function testGetter()
    {
        $model = $this->fixture->getModel('model1');

        $this->assertFalse($model->getIp());
    }   
}
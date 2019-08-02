<?php

namespace luya\errorapi\models;

use yii\base\BaseObject;

class Trace extends BaseObject
{
    public $file;
    public $line;
    public $function;
    public $class;
}
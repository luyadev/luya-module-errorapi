<?php

namespace luya\errorapi\models;

use yii\base\BaseObject;

class Trace extends BaseObject
{
    public $file;
    public $line;
    public $function;
    public $class;

    // since luya core version 1.0.20
    public $context_line;
    public $pre_context;
    public $post_context;
    public $abs_path;
}
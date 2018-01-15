<?php

use Curl\Curl;

require_once 'vendor/autoload.php';

$url = isset($argv[1]) ? $argv[1] : false;

if (!$url) {
    echo "You have to provide an url, example: php test.php http://localhost/luya-envs-dev/public_html/errorapi/create";
    exit(1);
}

$curl = new Curl();
$curl->post($url, ['error_json' => json_encode([
    'serverName' => 'foo',
    'message' => 'fooo',
    'trace' => [
        1 => ['message' => 'Yes', 'array' => [1,2,3]],
        2 => 'boom',
    ],
    'integer' => 1,
    'boolena' => false,
    'foobar' => [123,123],
])]);

var_dump($curl->response);
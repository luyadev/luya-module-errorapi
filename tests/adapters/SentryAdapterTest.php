<?php

namespace luya\errorapi\tests\adapters;

use luya\errorapi\tests\ErrorApiTestCase;
use luya\errorapi\adapters\SentryAdapter;

class SentryAdapterTest extends ErrorApiTestCase
{
    public function testSentryAdapter()
    {
        $json = '{
            "message":"Your request was made with invalid credentials.",
            "file":"/vendor/yiisoft/yii2/filters/auth/AuthMethod.php",
            "line":93,
            "requestUri":"/admin/api-admin-storage/file-replace",
            "serverName":"mytestdomain.com",
            "date":"29.07.2019 15:20",
            "trace":[
                {
                    "file":"/vendor/yiisoft/yii2/filters/auth/AuthMethod.php",
                    "line":76,
                    "function":"handleFailure",
                    "class":"AuthMethod"
                },
                {
                    "file":"/vendor/yiisoft/yii2/filters/auth/CompositeAuth.php",
                    "line":57,
                    "function":"beforeAction",
                    "class":"AuthMethod"
                }
            ],
            "previousException":[],
            "ip":"xxx.xxx.xxx.xxx",
            "get":[],
            "post":[],
            "bodyParams":[],
            "session":[],
            "server":{
                "REDIRECT_HTTPS":"on",
                "REDIRECT_SCRIPT_URL":"/admin/api-admin-storage/file-replace",
                "REDIRECT_SCRIPT_URI":"http://mytestdomain.com/admin/api-admin-storage/file-replace",
                "REDIRECT_HTTP_AUTHORIZATION":"",
                "REDIRECT_STATUS":"200",
                "HTTPS":"on",
                "SCRIPT_URL":"/admin/api-admin-storage/file-replace",
                "SCRIPT_URI":"http://mytestdomain.com/admin/api-admin-storage/file-replace",
                "HTTP_AUTHORIZATION":"",
                "HTTP_X_FORWARDED_FOR":"217.146.165.197",
                "HTTP_X_FORWARDED_PROTO":"https",
                "HTTP_X_FORWARDED_PORT":"443",
                "HTTP_HOST":"mytestdomain.com",
                "HTTP_USER_AGENT":"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36",
                "HTTP_ACCEPT":"text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
                "HTTP_ACCEPT_ENCODING":"gzip,deflate",
                "SERVER_SIGNATURE":"<address>Apache/2.4.29 (Ubuntu) Server at mytestdomain.com Port 80</address>",
                "SERVER_SOFTWARE":"Apache/2.4.29 (Ubuntu)",
                "SERVER_NAME":"mytestdomain.com",
                "SERVER_ADDR":"xxx.xxx.xxx.xxx",
                "SERVER_PORT":"80",
                "REMOTE_ADDR":"xxx.xxx.xxx.xxx",
                "DOCUMENT_ROOT":"/var/www/html/current/public_html",
                "REQUEST_SCHEME":"http",
                "CONTEXT_PREFIX":"",
                "CONTEXT_DOCUMENT_ROOT":"/var/www/html/current/public_html",
                "SERVER_ADMIN":"webmaster@localhost",
                "SCRIPT_FILENAME":"/var/www/html/current/public_html/index.php"
            }
        }';

        $model = $this->fixture->newModel;
        $model->id = 123;
        $model->identifier = 'xyz';
        $model->error_json = $json;
        $model->timestamp_create = time();

        $adapter = new SentryAdapter([
            'token' => getenv('sentryToken'),
            'organisation' => getenv('sentryOrganisation'),
            'team' => getenv('sentryTeam')
        ]);

        $this->assertSame([
            'transaction' => '/vendor/yiisoft/yii2/filters/auth/AuthMethod.php',
            'server_name' => 'mytestdomain.com',
            'metadata' => array (
                'value' => 'Your request was made with invalid credentials.',
                'filename' => '/vendor/yiisoft/yii2/filters/auth/AuthMethod.php',
            ),
            'fingerprint' => array (
                0 => '{{ default }}',
                1 => '/admin/api-admin-storage/file-replace',
            ),
            'logger' => 'luya.errorapi',
            'platform' => 'php',
            'sdk' => array (
                'name' => 'luya-errorapi',
                'version' => '2.0.0',
            ),
            'environment' => 'prod',
            'level' => 'error',
            'contexts' => array (
                'os' => array (
                    'version' => '10.0',
                    'name' => 'Windows',
                    'type' => 'os',
                ),
                'browser' => array (
                    'version' => '75.0.3770.100',
                    'name' => 'Chrome',
                    'type' => 'browser',
                )
            ),
            'tags' => array (
                'luya_version' => '1.0',
                'file' => '/vendor/yiisoft/yii2/filters/auth/AuthMethod.php',
                'url' => 'http://mytestdomain.com/admin/api-admin-storage/file-replace',
            ),
            'user' => array (
                'ip_address' => 'xxx.xxx.xxx.xxx',
            ),
            'extra' =>array (
                'request_uri' => '/admin/api-admin-storage/file-replace',
                'line' => 93,
                'post' => array (),
                'get' => array (),
                'server' => array (
                    'REDIRECT_HTTPS' => 'on',
                    'REDIRECT_SCRIPT_URL' => '/admin/api-admin-storage/file-replace',
                    'REDIRECT_SCRIPT_URI' => 'http://mytestdomain.com/admin/api-admin-storage/file-replace',
                    'REDIRECT_HTTP_AUTHORIZATION' => '',
                    'REDIRECT_STATUS' => '200',
                    'HTTPS' => 'on',
                    'SCRIPT_URL' => '/admin/api-admin-storage/file-replace',
                    'SCRIPT_URI' => 'http://mytestdomain.com/admin/api-admin-storage/file-replace',
                    'HTTP_AUTHORIZATION' => '',
                    'HTTP_X_FORWARDED_FOR' => '217.146.165.197',
                    'HTTP_X_FORWARDED_PROTO' => 'https',
                    'HTTP_X_FORWARDED_PORT' => '443',
                    'HTTP_HOST' => 'mytestdomain.com',
                    'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.100 Safari/537.36',
                    'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'HTTP_ACCEPT_ENCODING' => 'gzip,deflate',
                    'SERVER_SIGNATURE' => '<address>Apache/2.4.29 (Ubuntu) Server at mytestdomain.com Port 80</address>',
                    'SERVER_SOFTWARE' => 'Apache/2.4.29 (Ubuntu)',
                    'SERVER_NAME' => 'mytestdomain.com',
                    'SERVER_ADDR' => 'xxx.xxx.xxx.xxx',
                    'SERVER_PORT' => '80',
                    'REMOTE_ADDR' => 'xxx.xxx.xxx.xxx',
                    'DOCUMENT_ROOT' => '/var/www/html/current/public_html',
                    'REQUEST_SCHEME' => 'http',
                    'CONTEXT_PREFIX' => '',
                    'CONTEXT_DOCUMENT_ROOT' => '/var/www/html/current/public_html',
                    'SERVER_ADMIN' => 'webmaster@localhost',
                    'SCRIPT_FILENAME' => '/var/www/html/current/public_html/index.php',
                ),
                'session' => array (),
            ),
            'exception' => array (
                'values' => array (
                    0 => array(
                        'type' => 'Exception',
                        'value' => 'Your request was made with invalid credentials.',
                        'stacktrace' => array  (
                            'frames' => array (
                                0 => array (
                                    'filename' => '/vendor/yiisoft/yii2/filters/auth/AuthMethod.php',
                                    'function' => 'handleFailure',
                                    'lineno' => 76,
                                    'module' => 'AuthMethod',
                                ),
                                1 => array (
                                    'filename' => '/vendor/yiisoft/yii2/filters/auth/CompositeAuth.php',
                                    'function' => 'beforeAction',
                                    'lineno' => 57,
                                    'module' => 'AuthMethod',
                                )
                            )
                        )
                    )
                )
            )
            ], $adapter->generateStorePayload($model));

        $this->assertTrue($adapter->onCreate($model));
    }
}
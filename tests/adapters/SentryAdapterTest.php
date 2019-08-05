<?php

namespace luya\errorapi\tests\adapters;

use luya\errorapi\tests\ErrorApiTestCase;
use luya\errorapi\adapters\SentryAdapter;

class SentryAdapterTest extends ErrorApiTestCase
{
    public function testStackTraceFromProject()
    {
        $json = '{
            "message": "SQLSTATE[42S02]: Base table or view not found: 1146 Table luya.cms_redirect doesnt exist\nThe SQL being executed was: SELECT * FROM cms_redirect",
            "file": "/app/vendor/yiisoft/yii2/db/Schema.php",
            "line": "674",
            "requestUri": "/",
            "serverName": "luya",
            "date": "05.08.2019 15:46",
            "trace": [
              {
                "file": "/app/vendor/yiisoft/yii2/db/Command.php",
                "abs_path": "/app/vendor/yiisoft/yii2/db/Command.php",
                "line": "1295",
                "context_line": "                $e = $this->db->getSchema()->convertException($e, $rawSql);",
                "pre_context": [
                  "                } else {",
                  "                    $this->pdoStatement->execute();",
                  "                }",
                  "                break;",
                  "            } catch (\\\\Exception $e) {",
                  "                $rawSql = $rawSql ?: $this->getRawSql();"
                ],
                "post_context": [
                  "                if ($this->_retryHandler === null || !call_user_func($this->_retryHandler, $e, $attempt)) {",
                  "                    throw $e;",
                  "                }",
                  "            }",
                  "        }",
                  "    }"
                ],
                "function": "convertException",
                "class": "yii\\\\db\\\\Schema"
              },
              {
                "file": "/app/vendor/yiisoft/yii2/db/Command.php",
                "abs_path": "/app/vendor/yiisoft/yii2/db/Command.php",
                "line": "123",
                "context_line": "                $e = $this->db->getSchema()->convertException($e, $rawSql);",
                "pre_context": [
                  "                } else {",
                  "                    $this->pdoStatement->execute();",
                  "                }",
                  "                break;",
                  "            } catch (\\\\Exception $e) {",
                  "                $rawSql = $rawSql ?: $this->getRawSql();"
                ],
                "post_context": [
                  "                if ($this->_retryHandler === null || !call_user_func($this->_retryHandler, $e, $attempt)) {",
                  "                    throw $e;",
                  "                }",
                  "            }",
                  "        }",
                  "    }"
                ],
                "function": "convertException",
                "class": "yii\\\\db\\\\Schema"
              }
            ],
            "ip": "172.31.0.1",
            "get": [],
            "post": [],
            "bodyParams": [],
            "session": [],
            "yii_debug": "true",
            "yii_env": "prod",
            "status_code": "500",
            "exception_name": "Database Exception",
            "exception_class_name": "yii\\\\db\\\\Exception",
            "php_version": "7.2.17",
            "luya_version": "1.0.20"
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

        $e = $adapter->generateStorePayload($model);
        $this->assertTrue($adapter->onCreate($model));
    }
    
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

        $expect = [
            'transaction' => '/vendor/yiisoft/yii2/filters/auth/AuthMethod.php',
            'server_name' => 'mytestdomain.com',
            'metadata' => array (
                'value' => 'Your request was made with invalid credentials.',
                'filename' => '/vendor/yiisoft/yii2/filters/auth/AuthMethod.php',
            ),
            'fingerprint' => array (
                0 => 'Your request was made with invalid credentials.',
                1 => '/admin/api-admin-storage/file-replace'
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
                ),
                'runtime' => array (
                    'version' => 'unknown',
                    'type' => 'runtime',
                    'name' => 'php'
                )
            ),
            'tags' => array (
                'luya_version' => 'unknown',
                'php_version' => 'unknown',
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
                'yii_debug' => false,
                'yii_env' => 'prod',
                'http_status_code' => false,
                'exception_name' => false,
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
                                    'context_line' => null,
                                    'pre_context' => null,
                                    'post_context' => null,
                                    'abs_path' => null,
                                ),
                                1 => array (
                                    'filename' => '/vendor/yiisoft/yii2/filters/auth/CompositeAuth.php',
                                    'function' => 'beforeAction',
                                    'lineno' => 57,
                                    'context_line' => null,
                                    'pre_context' => null,
                                    'post_context' => null,
                                    'abs_path' => null,
                                )
                            )
                        )
                    )
                )
            )
        ];
        
        $this->assertSame($expect, $adapter->generateStorePayload($model));

        //$this->assertTrue($adapter->onCreate($model));
    }
}
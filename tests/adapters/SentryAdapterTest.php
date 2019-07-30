<?php

namespace luya\errorapi\tests\adapters;

use luya\errorapi\tests\ErrorApiTestCase;
use luya\errorapi\adapters\SentryAdapter;

class SentryAdapterTest extends ErrorApiTestCase
{
    /*
    public function testAdapter()
    {
        $model = $this->getDataFixture();

        $adapter = new SentryAdapter();
        $adapter->token = getenv('sentryToken');
        $adapter->organisation = getenv('sentryOrganisation');
        $adapter->team = getenv('sentryTeam');

        $this->assertTrue($adapter->onCreate($model->getModel('model1')));
    }
    */
    public function testFullErrorExample()
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
                "SERVER_SIGNATURE":"<address>Apache/2.4.29 (Ubuntu) Server at mytestdomain.com Port 80</address>\n",
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

        $adapter = new SentryAdapter();
        $adapter->token = getenv('sentryToken');
        $adapter->organisation = getenv('sentryOrganisation');
        $adapter->team = getenv('sentryTeam');

        $this->assertTrue($adapter->onCreate($model));
    }
}
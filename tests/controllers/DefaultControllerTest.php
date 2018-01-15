<?php

namespace tests\web\luya\errorapi\controllers;

use Yii;
use luya\errorapi\tests\ErrorApiTestCase;
use luya\testsuite\traits\MigrationFileCheckTrait;
use luya\errorapi\controllers\DefaultController;
use luya\errorapi\models\Data;

class DefaultControllerTest extends ErrorApiTestCase
{
    use MigrationFileCheckTrait;
    
    public function afterSetup()
    {
        Yii::$app->db->createCommand()->createTable('error_data', [
            'id' => 'INT(11) PRIMARY KEY',
            'identifier' => 'varchar(250)',
            'error_json' => 'TEXT',
            'timestamp_create' => 'int(11)',
        ])->execute();
    }
    
    public function testMigration()
    {
        $this->checkMigrationFolder('@errorapi/migrations');
    }
    
    public function testCreateEmptyData()
    {
        $response = Yii::$app->getModule('errorapi')->runAction('default/create');
        
        $this->assertEquals(true, is_array($response));
        
        // check fields
        $this->assertArrayHasKey('field', $response[0]);
        $this->assertArrayHasKey('message', $response[0]);
        
        // check field values
        $this->assertEquals($response[0]['field'], 'error_json');
        $this->assertEquals($response[0]['message'], 'Error Json cannot be blank.');
    }
    
    public function testCreateMissingData()
    {
        Yii::$app->request->setBodyParams(['error_json' => json_encode(['do' => 'fa'])]);
    
        $response = Yii::$app->getModule('errorapi')->runAction('default/create');
        $this->assertEquals(true, is_array($response));
        
        // check fields
        $this->assertArrayHasKey('field', $response[0]);
        $this->assertArrayHasKey('message', $response[0]);
        
        // check field values
        $this->assertEquals($response[0]['field'], 'error_json');
        $this->assertEquals($response[0]['message'], 'error_json must contain message and serverName keys with values.');
    }
    
    public function testModelJsonGetters()
    {
        $model = new Data();
        $model->error_json = json_encode(['serverName' => 'luya.io', 'message' => 'my message']);
        $model->save();
        
        $this->assertSame('my message', $model->getErrorMessage());
        $this->assertSame('luya.io', $model->getServerName());
    }
    
    public function testCreateData()
    {
        Yii::$app->request->setBodyParams(['error_json' => json_encode(['information' => ['foo' => 'bar'], 'message' => 'What?', 'serverName' => 'example.com', 'trace' => [['message' => 'yes', 'file' => 'file.php', 'line' => 11], ['message' => 'no', 'fle' => 'otherfile.php', 'line' => 27]]])]);
        
        $response = Yii::$app->getModule('errorapi')->runAction('default/create');
        
        $this->assertTrue($response);
    }
    
    public function testErrorMailView()
    {
        $ctrl = new DefaultController('default', Yii::$app->getModule('errorapi'));
        
        $data = new Data();
        $data->error_json = json_encode([
            'serverName' => 'luya.io',
            'message' => 'Hello World!',
            'violate' => '<script>alert(0)</script>',
            'trace' => [
                1 => ['file' => 'yes.php', 'line' => 123]
            ]
        ]);
        
        $html = $this->invokeMethod($ctrl, 'renderMail', [$data]);
        
        $expect = <<<EOT
<h1 style="color:#f00;">Hello World!</h1>
<p style="color:#800000;">from <strong>luya.io</strong></p>
<a href="https://github.com/luyadev/luya/issues/new?title=%23+Hello+World%21" target="_blank">Create new issue on GitHub</a>
<table cellspacing="2" cellpadding="6" border="0" width="100%">
<tr>
    <td width="150" style="background-color:#F0F0F0;"><strong>serverName:</strong></td>
    <td style="background-color:#F0F0F0;">
                    <code><span style="color: #000000">
<span style="color: #0000BB"></span><span style="color: #DD0000">'luya.io'</span>
</span>
</code>            </td>
</tr>
<tr>
    <td width="150" style="background-color:#F0F0F0;"><strong>message:</strong></td>
    <td style="background-color:#F0F0F0;">
                    <code><span style="color: #000000">
<span style="color: #0000BB"></span><span style="color: #DD0000">'Hello&nbsp;World!'</span>
</span>
</code>            </td>
</tr>
<tr>
    <td width="150" style="background-color:#F0F0F0;"><strong>violate:</strong></td>
    <td style="background-color:#F0F0F0;">
                    <code><span style="color: #000000">
<span style="color: #0000BB"></span><span style="color: #DD0000">'&lt;script&gt;alert(0)&lt;/script&gt;'</span>
</span>
</code>            </td>
</tr>
<tr>
    <td width="150" style="background-color:#F0F0F0;"><strong>trace:</strong></td>
    <td style="background-color:#F0F0F0;">
                    <table border="0" cellpadding="4" cellspacing="2" width="100%">
        <tr>
        <td style="background-color:#e1e1e1; text-align:center;" width="40">
            #1        </td>
        <td style="background-color:#e1e1e1;">
                        <table cellspacing="0" cellpadding="4" border="0">
                                <tr>
                    <td>file:</td><td><code><span style="color: #000000">
<span style="color: #0000BB"></span><span style="color: #DD0000">'yes.php'</span>
</span>
</code></td>
                </tr>
                                <tr>
                    <td>line:</td><td><code><span style="color: #000000">
<span style="color: #0000BB">123</span>
</span>
</code></td>
                </tr>
                            </table>
                    </td>
    </tr>
    </table>            </td>
</tr>
</table>
EOT;
        
        $this->assertSame($expect, $html);
        
    }
    
    protected function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }
}

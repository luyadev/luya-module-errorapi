<?php

namespace luya\errorapi\tests\controllers;

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
}

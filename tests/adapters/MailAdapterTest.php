<?php

namespace luya\errorapi\tests\adapters;

use luya\errorapi\tests\ErrorApiTestCase;
use luya\errorapi\adapters\MailAdapter;
use luya\errorapi\models\Data;

class MailAdapterTest extends ErrorApiTestCase
{
    public function testAdapter()
    {
        $adapter = new MailAdapter();
        $adapter->setModule($this->app->getModule('errorapi'));
        $fixture = $this->getDataFixture();
        
        $data = new Data();
        $data->error_json = json_encode([
            'serverName' => 'luya.io',
            'message' => 'Hello World!',
            'violate' => '<script>alert(0)</script>',
            'trace' => [
                1 => ['file' => 'yes.php', 'line' => 123]
            ]
        ]);

        $html = $adapter->renderMail($data);

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

    /*
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
    */
}
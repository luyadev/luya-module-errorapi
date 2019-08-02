<?php

use luya\errorapi\Module;
use luya\helpers\Html;
use yii\helpers\VarDumper;

/**
 * @var $model \luya\errorapi\models\Data The data model with the message.
 * @var $issueLink string The link for the create new issue button
 * @var $this \luya\web\View current view.
 */
?>
<h1 style="color:#f00;"><?= $model->getErrorMessage(); ?></h1>
<p style="color:#800000;">from <strong><?= $model->getServerName(); ?></strong></p>
<a href="<?= $issueLink; ?>" target="_blank"><?= Module::t('mail_create_issue') ?></a>
<table cellspacing="2" cellpadding="6" border="0" width="100%">
<?php foreach ($model->getErrorArray() as $key => $value): ?>
<tr>
    <td width="150" style="background-color:#F0F0F0;"><strong><?= Html::encode($key); ?>:</strong></td>
    <td style="background-color:#F0F0F0;">
        <?php if (strtolower($key) === 'trace' && is_array($value)): ?>
            <?= $this->render('_trace', ['data' => $value]); ?>
        <?php elseif (is_array($value)): ?>
            <table cellspacing="0" cellpadding="4" border="0">
                 <?php foreach ($value as $k => $v): ?>
                    <tr>
                        <td><?= Html::encode($k); ?>:</td>
                        <td>
                            <?php if (strtolower($k) == 'trace' && is_array($v)): ?>
                                <?= $this->render('_trace', ['data' => $v]); ?>
                            <?php else: ?>
                                <?= VarDumper::dumpAsString($v, 10, true); ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <?= VarDumper::dumpAsString($value, 10, true); ?>
        <?php endif;?>
    </td>
</tr>
<?php endforeach; ?>
</table>
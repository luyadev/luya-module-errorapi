<?php

use luya\helpers\Html;
use yii\helpers\VarDumper;

/**
 * @var $data array The array with key values for the trace.
 * @var $this \luya\web\View current view.
 */
?>
<table border="0" cellpadding="4" cellspacing="2" width="100%">
    <?php foreach ($data as $number => $trace): ?>
    <tr>
        <td style="background-color:#e1e1e1; text-align:center;" width="40">
            #<?= Html::encode($number); ?>
        </td>
        <td style="background-color:#e1e1e1;">
            <?php if (is_array($trace)): ?>
            <table cellspacing="0" cellpadding="4" border="0">
                <?php foreach ($trace as $kt => $vt): ?>
                <tr>
                    <td><?= Html::encode($kt); ?>:</td><td><?= VarDumper::dumpAsString($vt, 10, true); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php else: ?>
                <?= VarDumper::dumpAsString($trace, 10, true); ?>
            <?php endif; ?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
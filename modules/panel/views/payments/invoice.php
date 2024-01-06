<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\JqueryAsset;
use yii\web\View;

/* @var $this yii\web\View */

$this->title = 'Счет-фактура';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('/css/bootstrap-datepicker.min.css', ['position' => View::POS_HEAD]);
$this->registerJsFile('/js/bootstrap-datepicker.min.js', ['depends' => [JqueryAsset::className()], 'position' => View::POS_HEAD]);
?>
<div class="payments-index">

    <div class="row mb20">
        <div class="col-md-6">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <?= Html::beginForm(['/payments/invoice'], 'get') ?>
    <div class="row mb20">
        <div class="col-md-2">
            <?= Html::dropDownList('user', $user, \app\models\Radio::getList(), ['class' => 'form-control']) ?>
        </div>
        <div class="col-md-2">
            <?= Html::dropDownList('month', $month, \app\models\Payments::$months, ['class' => 'form-control']) ?>
        </div>
        <div class="col-md-2">
            <?= Html::dropDownList('year', $year, \app\models\Payments::getYears(), ['class' => 'form-control']) ?>
        </div>
        <div class="col-md-2">
            <?= Html::submitButton('Показать', ['class' => 'btn btn-success']) ?>
        </div>
    </div>
    <?= Html::endForm() ?>

    <?php if (!empty($data)): ?>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Радио</th>
                <th>Дата</th>
                <th>Оператор</th>
                <th>КН</th>
                <th>КС</th>
                <th>Кол.</th>
                <th class="text-right">Сумма</th>
                <th class="text-right">Курс</th>
                <th class="text-right">Итого</th>
            </tr>
            </thead>
            <tbody>
            <?php $i = 0;
            $total = 0;
            $monthTotal = 0;
            foreach ($data as $row): if ($i == 0) $oldDate = date_create($row['year'] . '-' . $row['month'] . '-01'); ?>
                <?php $newDate = date_create($row['year'] . '-' . $row['month'] . '-01');
                if ($oldDate != $newDate):
                    $oldDate = $newDate;
                    ?>
                    <tr class="bold">
                        <td colspan="8">Итого:</td>
                        <td class="text-right"><?= number_format($monthTotal, 2, '.', ' ') ?> UZS</td>
                    </tr>
                    <?php $monthTotal = 0; endif; ?>
                <tr>
                    <td><?= $row['radio_name'] ?></td>
                    <td><?= \app\models\Payments::$months[(int)$row['month']] ?> <?= $row['year'] ?></td>
                    <td><?= $row['operator'] ?></td>
                    <td><?= $row['short_number'] ?></td>
                    <td><?= $row['keyword'] ?></td>
                    <td><?= $row['messages_count'] ?></td>
                    <td class="text-right"><?= number_format($row['price'], 2, '.', ' ') ?> <?= $row['currency'] ?></td>
                    <td class="text-right"><?= number_format($row['course'], 2, '.', ' ') ?></td>
                    <td class="text-right"><?= number_format($row['local_sum'], 2, '.', ' ') ?> UZS</td>
                </tr>
                <?php
                $i++;
                $total += round($row['local_sum'], 2);
                $monthTotal += round($row['local_sum'], 2);
            endforeach; ?>
            <tr class="bold">
                <td colspan="8">Итого:</td>
                <td class="text-right"><?= number_format($monthTotal, 2, '.', ' ') ?> UZS</td>
            </tr>
            <tr class="bold">
                <td colspan="8"></td>
                <td class="text-right"><?= number_format($total, 2, '.', ' ') ?> UZS</td>
            </tr>
            </tbody>
        </table>
    <?php else: ?>
        <div class="row">
            <p>Результатов не найдено</p>
        </div>
    <?php endif; ?>
</div>

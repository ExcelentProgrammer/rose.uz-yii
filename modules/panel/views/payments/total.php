<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\JqueryAsset;
use yii\web\View;

/* @var $this yii\web\View */

$this->title = 'Оплаты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payments-index">
    <div class="row mb20">
        <div class="col-md-6">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <?= Html::beginForm(['/payments/total'], 'get', ['class' => 'form-inline']) ?>
    <div class="mb20">
        <div class="form-group mr20">
            <label class="control-label mr10" for="start">Дата с:</label>
            <?= Html::textInput('start', $start, ['class' => 'form-control datepicker', 'id' => 'start']) ?>
        </div>
        <div class="form-group mr20">
            <label class="control-label mr10" for="end">по:</label>
            <?= Html::textInput('end', $end, ['class' => 'form-control datepicker', 'id' => 'end']) ?>
        </div>
        <?= Html::submitButton('Показать', ['class' => 'btn btn-success']) ?>
    </div>
    <?= Html::endForm() ?>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>Сервис</th>
            <th>Сумма</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Payme</td>
            <td><?= number_format($data['payme'], 2, '.', ' ') ?> UZS</td>
        </tr>
        <tr>
            <td>Click</td>
            <td><?= number_format($data['click'], 2, '.', ' ') ?> UZS</td>
        </tr>
        <tr>
            <td>Paynet</td>
            <td><?= number_format($data['paynet'], 2, '.', ' ') ?> UZS</td>
        </tr>
        <tr class="bold">
            <td>Итого</td>
            <td><?= number_format($data['payme'] + $data['click'] + $data['paynet'], 2, '.', ' ') ?> UZS</td>
        </tr>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    $('.datepicker').datepicker({
        "todayHighlight": true,
        "autoclose": true,
        "format": "yyyy-mm-dd",
        "language": "de"
    });
</script>
<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\JqueryAsset;
use yii\web\View;

/* @var $this yii\web\View */

$this->title = 'Статистика';
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile('/css/bootstrap-datepicker.min.css', ['position' => View::POS_HEAD]);
$this->registerJsFile('/js/bootstrap-datepicker.min.js', ['depends' => [JqueryAsset::className()], 'position' => View::POS_HEAD]);
?>
<div class="payments-index">

    <div class="row mb20">
        <div class="col-md-6">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-md-6">
            <div class="hd-btn pull-right">
                <?= Html::a("Счет-фактура", ['invoice'], ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>

    <?= Html::beginForm(['/payments/stats'], 'get') ?>
    <div class="row mb20">
        <div class="col-md-2">
            <?= Html::textInput('start', $start, ['class' => 'form-control', 'data-plugin' => 'date-picker']) ?>
        </div>
        <div class="col-md-2">
            <?= Html::textInput('end', $end, ['class' => 'form-control', 'data-plugin' => 'date-picker']) ?>
        </div>
        <div class="col-md-2">
            <?= Html::dropDownList('radio_id', $radio_id, \app\models\Radio::getList(), ['class' => 'form-control', 'prompt' => 'Радио']) ?>
        </div>
        <div class="col-md-2">
            <?= Html::dropDownList('short_number', $short_number, \app\models\ShortNumbers::getList(), ['class' => 'form-control', 'prompt' => 'КН']) ?>
        </div>
        <div class="col-md-2">
            <?= Html::textInput('keyword', $keyword, ['class' => 'form-control', 'placeholder' => 'КС']) ?>
        </div>
        <div class="col-md-2">
            <?= Html::submitButton('Показать', ['class' => 'btn btn-success']) ?>
        </div>
    </div>
    <?= Html::endForm() ?>

    <table class="table table-striped">
        <thead>
        <tr>
            <th>Дата</th>
            <th>Beeline</th>
            <th>Ucell</th>
            <th>UzMobile</th>
            <th>UzMobile GSM</th>
            <th>Perfectum</th>
            <th>Telegram</th>
            <th>UMS</th>
            <th>Остальные</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 0;
        $beeline = 0;
        $ucell = 0;
        $uzm1 = 0;
        $uzm2 = 0;
        $perfectum = 0;
        $telegram = 0;
        $ums = 0;
        $others = 0;
        foreach ($data as $row): ?>
            <tr>
                <td><?= date_format(date_create($row['date']), "d.m.y") ?></td>
                <td><?= $row['Beeline_cnt'] ?></td>
                <td><?= $row['Ucell_cnt'] ?></td>
                <td><?= $row['Uzmobile_CDMA_cnt'] ?></td>
                <td><?= $row['Uzmobile_GSM_cnt'] ?></td>
                <td><?= $row['Perfectum_cnt'] ?></td>
                <td><?= $row['Telegram_cnt'] ?></td>
                <td><?= $row['UMS_cnt'] ?></td>
                <td><?= $row['Others_cnt'] ?></td>
            </tr>
            <?php
            $beeline += $row['Beeline_cnt'];
            $ucell += $row['Ucell_cnt'];
            $uzm1 += $row['Uzmobile_CDMA_cnt'];
            $uzm2 += $row['Uzmobile_GSM_cnt'];
            $perfectum += $row['Perfectum_cnt'];
            $telegram += $row['Telegram_cnt'];
            $ums += $row['UMS_cnt'];
            $others += $row['Others_cnt'];
        endforeach;
        ?>
        <tr class="bold">
            <td>Итого:</td>
            <td><?= $beeline ?></td>
            <td><?= $ucell ?></td>
            <td><?= $uzm1 ?></td>
            <td><?= $uzm2 ?></td>
            <td><?= $perfectum ?></td>
            <td><?= $telegram ?></td>
            <td><?= $ums ?></td>
            <td><?= $others ?></td>
        </tr>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    $('input[data-plugin=date-picker]').datepicker({
        "todayHighlight": true,
        "autoclose": true,
        "format": "yyyy-mm-dd",
        "language": "ru"
    });
</script>
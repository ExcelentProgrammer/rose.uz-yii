<?php

use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use app\models\Dashboard;

/* @var $this yii\web\View */
/* @var $start string */
/* @var $end string */
/* @var $dataProvider \yii\data\ArrayDataProvider */

$this->title = 'Статистика';
$this->params['breadcrumbs'][] = $this->title;

$total = 0;
$count = 0;
$cheque = 0;

foreach ($dataProvider->models as $row) {
    $total += $row['total'];
    $count += $row['count'];
}
?>
<div class="card">
    <div class="header clearfix">
        <h2 class="pull-left title"><?= Html::encode($this->title) ?></h2>
    </div>
    <hr>
    <div class="content">
        <div class="row">
            <?php $form = ActiveForm::begin([
                'id' => 'filterForm',
                'method' => 'get',
                'action' => ['statistics/index']
            ]); ?>
            <div class="col-md-2 mb20">
                <?= DatePicker::widget([
                    'name' => 'start',
                    'removeButton' => false,
                    'value' => $start,
                    'pluginOptions' => [
                        'todayHighlight' => true,
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd'
                    ],
                    'options' => [
                        'autocomplete' => 'off'
                    ]
                ]); ?>
            </div>
            <div class="col-md-2 mb20">
                <?= DatePicker::widget([
                    'name' => 'end',
                    'removeButton' => false,
                    'value' => $end,
                    'pluginOptions' => [
                        'todayHighlight' => true,
                        'autoclose' => true,
                        'format' => 'yyyy-mm-dd'
                    ],
                    'options' => [
                        'autocomplete' => 'off'
                    ]
                ]); ?>
            </div>
            <div class="col-md-3 mb20">
                <?= Html::submitButton('Показать', ['class' => 'btn btn-primary btn-fill']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <hr>
        <div class="statistics table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'summary' => false,
                'showFooter' => true,
                'tableOptions' => [
                    'class' => 'table table-striped table-hover report-table',
                ],
                'columns' => [
                    [
                        'class' => 'yii\grid\SerialColumn',
                        'contentOptions' => [
                            'width' => 40,
                            'class' => 'text-center'
                        ]
                    ],
                    [
                        'attribute' => 'date',
                        'label' => 'Дата',
                        'value' => function ($model) {
                            return date("d.m.Y", strtotime($model['date']));
                        },
                        'footer' => 'Итого:',
                        'footerOptions' => ['class' => 'text-left bold'],
                    ],
                    [
                        'attribute' => 'count',
                        'label' => 'Количество',
                        'value' => function ($row) {
                            return $row['count'] . ' шт';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'headerOptions' => ['class' => 'text-center'],
                        'footerOptions' => ['class' => 'text-center bold'],
                        'footer' => $count . ' шт'
                    ],
                    [
                        'label' => 'Средний чек',
                        'value' => function ($row) {
                            return Dashboard::price($row['total'] / $row['count']) . ' сум';
                        },
                        'contentOptions' => ['class' => 'text-center'],
                        'headerOptions' => ['class' => 'text-center'],
                        'footerOptions' => ['class' => 'text-center bold'],
                        'footer' => Dashboard::price($total / $count) . ' сум'
                    ],
                    [
                        'attribute' => 'total',
                        'label' => 'Итого',
                        'value' => function ($row) {
                            return Dashboard::price($row['total']) . ' сум';
                        },
                        'contentOptions' => ['class' => 'text-right'],
                        'headerOptions' => ['class' => 'text-right'],
                        'footerOptions' => ['class' => 'text-right bold'],
                        'footer' => Dashboard::price($total) . ' сум'
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>

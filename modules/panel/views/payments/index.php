<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PaymentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Детализация';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payments-index">

    <div class="row mb20">
        <div class="col-md-6">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
        <div class="col-md-6">
            <div class="hd-btn pull-right">
                <?= Html::a("Итого", ['total'], ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            [
                'attribute' => 'chat_id',
                'value' => 'chat.first_name',
                'filter' => \app\models\Chats::getChats()
            ],
            [
                'attribute' => 'radio',
                'value' => function ($model) {
                    return ($model->radio == 0) ? "Оплата" : $model->radios->name;
                }
            ],
            [
                'attribute' => 'type',
                'value' => function ($model) {
                    return ($model->type) ? "Оплата" : "Расход";
                },
                'filter' => [0 => "Расход", 1 => "Оплата"]
            ],
            'amount',
            [
                'attribute' => 'service',
                'value' => function ($model) {
                    return $model->service;
                },
                'filter' => \app\models\Payments::getServiceFilter()
            ],
            [
                'attribute' => 'date',
                'value' => function ($model) {
                    $date = date_create($model->date);
                    if (date_format($date, "Y") == date("Y"))
                        return date_format($date, "d M H:i");
                    else
                        return date_format($date, "d M y H:i");
                },
//                'filter' => '<in'
            ],
            // 'description',
            'balance',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'contentOptions' => [
                    'width' => 30,
                    'class' => 'center'
                ]
            ],
        ],
    ]); ?>
</div>
<script type="text/javascript">
    $('table.table .filters input[name="PaymentSearch[date]"]').datepicker({
        "todayHighlight": true,
        "autoclose": true,
        "format": "yyyy-mm-dd",
        "language": "de"
    });
</script>
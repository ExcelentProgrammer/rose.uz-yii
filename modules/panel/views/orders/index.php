<?php

use app\models\Bots;
use app\models\Orders;
use kartik\date\DatePicker;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Заказы';
$this->params['breadcrumbs'][] = $this->title;

$js = <<<JS
 $('body').on('change', 'select[data-act=change-state]', function () {
        var id = $(this).attr('data-id');
        var state = $(this).val();
        post('/panel/ajax/change-state', {id: id, state: state}, function (r) {
            if (r.state == "OK") {
                alert("Статус изменился");
            }
        });
        return false;
    });
JS;


$this->registerJs($js, \yii\web\View::POS_READY);

$css = <<<CSS
.grid-view {
    overflow: auto;
}
CSS;

//$this->registerCss($css, \yii\web\View::POS_READY);

?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="header clearfix">
                <h2 class="title"><?= Html::encode($this->title) ?></h2>
            </div>
            <hr>
            <div class="content" style="overflow: scroll;">
                <?php if (Yii::$app->session->hasFlash('error')): ?>
                    <div class="alert alert-danger alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <?= Yii::$app->session->getFlash('error') ?>
                    </div>
                <?php endif; ?>

                <?php if (Yii::$app->session->hasFlash('success')): ?>
                    <div class="alert alert-success alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <?= Yii::$app->session->getFlash('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (Yii::$app->session->hasFlash('delete')): ?>
                    <div class="alert alert-success alert-dismissable">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        <?= Yii::$app->session->getFlash('delete') ?>
                    </div>
                <?php endif; ?>
                <?php Pjax::begin(['timeout' => false, 'id' => 'pjax-gridview']); ?>
                <div class="bots-index">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => [
                            'class' => 'table table-hover',
                        ],
                        'rowOptions' => function ($model) {
                            $class = "";
                            if ($model->state > 1 && $model->state <= 4)
                                $class = "bg-orange";
                            elseif ($model->state == 5)
                                $class = "bg-green";
                            elseif ($model->state == 6)
                                $class = "bg-red";
                            return ['class' => $class];
                        },
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],

                            [
                                'attribute' => 'id',
                                'value' => function ($row) {
                                    return Html::a($row->id, ['view', 'id' => $row->id]);
                                },
                                'format' => 'raw',
                                'contentOptions' => [
                                    'width' => 80,
                                    'class' => 'text-center'
                                ]
                            ],
//            'chat_id',
//            [
//                'attribute' => 'client_type',
//                'value' => function ($row) {
//                    return Orders::$clientTypes[$row->client_type];
//                },
//                'filter' => Orders::$clientTypes
//            ],
//            [
//                'attribute' => 'system_id',
//                'value' => function ($row) {
//                    return $row->system->name;
//                },
//                'filter' => Bots::getList()
//            ],
//            'date',
                            'sender_name',
                            'sender_phone',
                            // 'sender_email:email',
//            'receiver_name',
//            'receiver_phone',
                            'receiver_address:ntext',
                            [
                                'attribute' => 'delivery_date',
                                'filter' => DatePicker::widget([
                                    'model' => $searchModel,
                                    'attribute' => 'delivery_date',
                                    'type' => DatePicker::TYPE_INPUT,
                                    'removeButton' => false,
                                    'pluginOptions' => [
                                        'todayHighlight' => true,
                                        'autoclose' => true,
                                        'format' => 'yyyy-mm-dd'
                                    ],
                                    'options' => [
                                        'autocomplete' => 'off'
                                    ]
                                ]),
                                'value' => function ($row) {
                                    return date("d.m.Y", strtotime($row->delivery_date));
                                },
                                'contentOptions' => [
                                    'width' => 100,
                                    'class' => 'text-center'
                                ]
                            ],
                            // 'delivery_price',
                            // 'know_address',
//            [
//                'attribute' => 'add_card',
//                'value' => function ($row) {
//                    return $row->add_card ? "Да" : "Нет";
//                },
//                'filter' => ["Нет", "Да"],
//                'contentOptions' => [
//                    'width' => 80,
//                    'class' => 'text-center'
//                ]
//            ],
//            [
//                'attribute' => 'take_photo',
//                'value' => function ($row) {
//                    return $row->take_photo ? "Да" : "Нет";
//                },
//                'filter' => ["Нет", "Да"],
//                'contentOptions' => [
//                    'width' => 80,
//                    'class' => 'text-center'
//                ]
//            ],
                            // 'card_text:ntext',
                            [
                                'label' => 'Итого',
                                'value' => function ($row) {
                                    return $row->getOrderTotalPrice() . " сум";
                                },
                                'contentOptions' => [
                                    'width' => 120,
                                    'class' => 'text-center'
                                ]
                            ],
                            [
                                'attribute' => 'total_paid',
                                'value' => function ($row) {
                                    return number_format($row->total_paid, 0, '.', ' ') . " сум";
                                },
                                'contentOptions' => [
                                    'width' => 120,
                                    'class' => 'text-center'
                                ]
                            ],
                            [
                                'attribute' => 'state',
                                'value' => function ($row) {
                                    return Html::dropDownList('state', $row->state, Orders::$statuses, ['class' => 'form-control', 'data-act' => 'change-state', 'data-id' => $row->id]);
                                },
                                'format' => 'raw',
                                'filter' => Orders::$statuses,
                                'contentOptions' => [
                                    'width' => '150px'
                                ]
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{delete}',
                                'contentOptions' => [
                                    'width' => 20,
                                    'class' => 'text-center'
                                ]
                            ],
                        ],
                    ]); ?>
                    <?php Pjax::end(); ?>
                </div>
            </div>
        </div>
    </div>
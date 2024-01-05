<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Orders */

$this->title = 'Заказ #' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Заказы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <p class="header pull-right" style="margin-bottom: 30px;">
                <img src="https://rose.uz/img/logo.png" style="height: 60px;">
            </p>
            <div class="header clearfix">
                <h2 class="title"><?= Html::encode($this->title) ?> <span class="fa fa-print print-btn hidden-print"
                                                                          data-act="printOrder"></span></h2>
            </div>
            <div id="cardDetail" class="content table-responsive">
                <div class="row">
                    <div class="col-sm-7">
                        <?= DetailView::widget([
                            'model' => $model,
                            'options' => [
                                'class' => 'table table-hover detail-view'
                            ],
                            'attributes' => [
//                                'id',
//                    'chat_id',
                                'sender_name',
                                'sender_phone',
                                'sender_email:email',
                                [
                                    'attribute' => 'receiver_name',
                                    'value' => $model->receiver_name . ' __________________',
                                ],
                                'receiver_phone',
                                [
                                    'attribute' => 'delivery_date',
                                    'value' => date_format(date_create($model->delivery_date), "d.m.Y"),
                                    'captionOptions' => [
                                        'width' => 200
                                    ],
                                ],
                                [
                                    'attribute' => 'delivery_period',
                                    'value' => \app\models\Orders::$period[$model->delivery_period],
                                    'captionOptions' => [
                                        'width' => 200
                                    ],
                                ],
                                'receiver_address:ntext',
                                [
                                    'attribute' => 'know_address',
                                    'value' => $model->know_address ? "Да" : "Нет",
                                    'contentOptions' => [
                                        'class' => 'hidden-print'
                                    ],
                                    'captionOptions' => [
                                        'class' => 'hidden-print'
                                    ],
                                ],
                                [
                                    'attribute' => 'add_card',
                                    'value' => $model->add_card ? "Да" : "Нет"
                                ],
                                [
                                    'attribute' => 'take_photo',
                                    'value' => $model->take_photo ? "Да" : "Нет"
                                ],
                                'card_text:ntext',
//                                [
//                                    'attribute' => 'system_id',
//                                    'value' => $model->system->name,
//                                    'contentOptions' => [
//                                        'class' => 'hidden-print'
//                                    ],
//                                    'captionOptions' => [
//                                        'class' => 'hidden-print'
//                                    ],
//                                ],
//                                [
//                                    'attribute' => 'client_type',
//                                    'contentOptions' => [
//                                        'class' => 'hidden-print'
//                                    ],
//                                    'captionOptions' => [
//                                        'class' => 'hidden-print'
//                                    ],
//                                ],
                                [
                                    'attribute' => 'payment_type',
                                    'contentOptions' => [
                                        'class' => 'hidden-print'
                                    ],
                                    'captionOptions' => [
                                        'class' => 'hidden-print'
                                    ],
                                ],
                                [
                                    'attribute' => 'date',
                                    'value' => date_format(date_create($model->date), "d.m.Y H:i"),
                                    'contentOptions' => [
                                        'class' => 'hidden-print'
                                    ],
                                    'captionOptions' => [
                                        'class' => 'hidden-print'
                                    ],
                                ],
                                [
                                    'attribute' => 'state',
                                    'value' => \app\models\Orders::$statuses[$model->state],
                                    'contentOptions' => [
                                        'class' => 'hidden-print'
                                    ],
                                    'captionOptions' => [
                                        'class' => 'hidden-print'
                                    ],
                                ],
                                [
                                    'attribute' => 'total_paid',
                                    'value' => number_format($model->total_paid, 2, '.', ' ') . ' UZS',
//                                    'visible' => false,
                                    'contentOptions' => [
                                        'class' => 'hidden-print'
                                    ],
                                    'captionOptions' => [
                                        'class' => 'hidden-print'
                                    ],
                                ],
                            ],
                        ]) ?>
                    </div>
                    <div class="col-sm-5">
                        <table class="table table-hover">
                            <tbody>
                            <?php $i = 0;
                            foreach ($model->items as $item):
                                $i++; ?>
                                <tr>
                                    <td width="200">
                                        <?= Html::img(Yii::$app->homeUrl . 'uploads/products/' . $item->product->photo, ['class' => 'img-response', 'width' => 200]) ?>
                                    </td>
                                    <td>
                                        <p><b><?= $item->product_name ?></b></p>
                                        <p><?= $item->amount ?> шт</p>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
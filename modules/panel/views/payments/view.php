<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Payments */

$this->title = $model->chat->first_name;
$this->params['breadcrumbs'][] = ['label' => 'Детализация', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payments-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'chat_id',
                'value' => $model->chat->first_name
            ],
            [
                'attribute' => 'radio',
                'value' => ($model->radio == 0) ? "Оплата" : $model->radios->name],
            [
                'attribute' => 'type',
                'value' => ($model->type) ? "Оплата" : "Расход"
            ],
            'amount',
            'service',
            'date',
            'description',
            'balance',
        ],
    ]) ?>

</div>

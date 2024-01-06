<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Chats */

$this->title = $model->first_name;
$this->params['breadcrumbs'][] = ['label' => 'Чаты', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="chats-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'chat_id',
            'first_name',
            'last_name',
            'username',
            'type',
            'lang',
        ],
    ]) ?>

</div>

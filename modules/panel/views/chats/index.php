<?php

use yii\grid\GridView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ChatSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Чаты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="chats-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'table table-hover',
        ],
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'chat_id',
            'first_name',
            'last_name',
            'username',
//            'type',
//            'lang',
            'phone',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}',
                'contentOptions' => [
                    'width' => 50,
                    'class' => 'center'
                ]
            ],
        ],
    ]); ?>
</div>

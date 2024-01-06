<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Страницы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
  <div class="header clearfix">
    <h2 class="pull-left title"><?= Html::encode($this->title) ?></h2>
    <p class="pull-right">
      <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success btn-fill']) ?>
    </p>
  </div>
  <hr>
  <div class="content">
    <div class="table-responsive">
      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'summary' => false,
        'tableOptions' => [
          'class' => 'table table-striped table-hover',
        ],
        'columns' => [
          [
            'class' => 'yii\grid\SerialColumn',
            'contentOptions' => [
              'width' => 40,
              'class' => 'text-center'
            ]
          ],
//                'id',
          [
            'attribute' => 'title_ru',
            'format' => 'raw',
            'value' => function ($model) {
              return Html::a($model->title_ru, ['pages/view', 'id' => $model->id]);
            }
          ],
          'title_uz',
          'title_en',
          'url',
          //'text_ru:ntext',
          //'text_uz:ntext',
          //'text_en:ntext',
          //'date',
          //'author',
          [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view} {update}',
            'contentOptions' => [
              'width' => 60,
              'class' => 'text-center'
            ]
          ],
        ],
      ]); ?>
    </div>
  </div>
</div>

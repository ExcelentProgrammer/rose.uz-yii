<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Pages */

$this->title = $model->title_ru;
$this->params['breadcrumbs'][] = ['label' => 'Страницы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
  <div class="header clearfix">
    <h2 class="title pull-left"><?= $this->title ?></h2>
    <p class="pull-right">
      <?= Html::a('Назад', ['index'], ['class' => 'btn btn-primary btn-fill']) ?>
    </p>
  </div>
  <div class="content">
    <?= DetailView::widget([
      'model' => $model,
      'options' => [
        'class' => 'table table-hover detail-view'
      ],
      'attributes' => [
//                'id',
        [
          'attribute' => 'image',
          'format' => 'raw',
          'value' => Html::img(Yii::$app->params['cdnUrl'] . 'pages/' . $model->image, ['class' => 'img-responsive'])
        ],
        'url:url',
        'title_ru',
        [
          'attribute' => 'text_ru',
          'format' => 'html'
        ],
        'title_uz',
        [
          'attribute' => 'text_uz',
          'format' => 'html'
        ],
        'title_en',
        [
          'attribute' => 'text_en',
          'format' => 'html'
        ],
        'date',
        'author',
      ],
    ]) ?>
  </div>
</div>

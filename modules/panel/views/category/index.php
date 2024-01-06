<?php

use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Категории';
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
    <div class="table-responsive bots-index">
      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
          'class' => 'table table-hover',
        ],
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
          [
            'class' => 'yii\grid\SerialColumn',
            'options' => [
              'width' => 40,
              'class' => 'text-center'
            ]
          ],
//                            [
//                                'attribute' => 'id',
//                                'contentOptions' => [
//                                    'width' => '80'
//                                ]
//                            ],
//                            [
//                                'attribute' => 'bot_id',
//                                'value' => function ($model) {
//                                    return $model->bot->name;
//                                },
//                                'filter' => \app\models\Bots::getList()
//                            ],
          'name',
          'name_uz',
          'name_uc',
          'name_en',
          [
            'attribute' => 'position',
            'contentOptions' => [
              'width' => 40,
              'class' => 'text-center'
            ]
          ],

          [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update} {delete}',
            'contentOptions' => [
              'width' => 60,
              'class' => 'text-center'
            ]
          ],
        ],
      ]); ?>
    </div>
    <?php Pjax::end(); ?>
  </div>
</div>
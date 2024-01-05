<?php

use app\models\Category;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Products */
/* @var $searchModel app\models\ProductSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Продукты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="header clearfix">
                <h2 class="pull-left title"><?= Html::encode($this->title) ?></h2>
                <p class="pull-right">
                    <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success btn-fill']) ?>
                </p>
            </div>
            <hr>
            <div class="content table-responsive">
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
                <div class="bots-index">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => [
                            'class' => 'table table-hover',
                        ],
                        'columns' => [

                            [
                                'class' => 'yii\grid\SerialColumn',
                                'options' => [
                                    'width' => 40,
                                    'class' => 'text-center'
                                ]
                            ],
                            [
                                'attribute' => 'photo',
                                'value' => function ($model) {
                                    return '<div class="text-center">' . Html::img(Yii::$app->homeUrl . 'uploads/products/' . $model->photo, ['class' => 'w150']) . '</div>';
                                },
                                'filter' => false,
                                'format' => 'raw'
                            ],

                            [
                                'attribute' => 'category_id',
                                'value' => function ($model) {
                                    return $model->getCategoriesList();
                                },
                                'filter' => Category::getList()
                            ],
                            [
                                'attribute' => 'name',
                                'contentOptions' => [
                                ]
                            ],
                            'description:ntext',
                            [
                                'attribute' => 'price',
                                'value' => function ($model) {
                                    return number_format($model->price, 2, '.', ' ') . ' UZS';
                                },
                                'contentOptions' => [
                                    'width' => '140',
                                    'class' => 'text-center text-nowrap'
                                ]
                            ],

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'template' => '{update} {delete}',
                                'contentOptions' => [
                                    'width' => '60',
                                    'class' => 'text-center'
                                ]
                            ],
                        ],
                    ]); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php

use app\models\User;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
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
                <?php Pjax::begin(['timeout' => false, 'id' => 'pjax-gridview']); ?>
                <div class="bots-index">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'tableOptions' => [
                            'class' => 'table table-hover',
                        ],
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'options' => [
                                    'width' => 40,
                                    'class'=>'text-center'
                                ]
                            ],
                            'fullname',
                            'username',
                            [
                                'attribute' => 'role',
                                'value' => function ($data) {
                                    return User::$roles[$data->role];
                                }
                            ],
                            'phone',
//             'regDate',
                            [
                                'attribute' => 'state',
                                'value' => function ($data) {
                                    return User::$states[$data->state];
                                }
                            ],
                            [
                                'attribute' => 'lastVisit',
                                'value' => function ($model) {
                                    $date = date_create($model->lastVisit);
                                    if (date_format($date, "Y") == date("Y"))
                                        return date_format($date, "d M H:i");
                                    else
                                        return date_format($date, "d M y H:i");
                                }
                            ],

                            [
                                'class' => 'yii\grid\ActionColumn',
                                'contentOptions' => [
                                    'width' => 80,
                                    'class' => 'center'
                                ]
                            ],
                        ],
                    ]); ?>
                </div>
                <?php Pjax::end(); ?>
            </div>
        </div>
    </div>
</div>
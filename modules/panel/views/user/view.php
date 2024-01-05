<?php

use app\models\User;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => 'Пользователи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-offset-3 col-md-6">
        <div class="card">
            <div class="header clearfix">
                <h2 class="title"><?= Html::encode($this->title) ?></h2>
            </div>
            <hr>
            <div class="content table-responsive">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        [
                            'attribute' => 'state',
                            'value' => User::$states[$model->state]
                        ],
                        [
                            'attribute' => 'role',
                            'value' => User::$roles[$model->role]
                        ],
                        'username',
//            'password',
                        'fullname',
                        'phone',
                        'email:email',
//            'photo',
//            'authKey',
//            'accessToken',
                        'regDate',
                        'lastVisit',
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>

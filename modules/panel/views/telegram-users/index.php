<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TelegramUserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Чаты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="header clearfix">
                <h2 class="title"><?= Html::encode($this->title) ?></h2>
            </div>
            <div class="content table-responsive">
                <?php Pjax::begin(['timeout' => false, 'id' => 'pjax-gridview']); ?>
                <div class="bots-index">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'tableOptions' => [
                            'class' => 'table table-hover',
                        ],
                        'columns' => [
//                            ['class' => 'yii\grid\SerialColumn'],

                            'id',
                            'first_name',
                            'last_name',
                            'username',
                            'chat_type',
                             'lang',
                             'phone',
                            // 'is_bot',
                             'is_admin',

//                            ['class' => 'yii\grid\ActionColumn'],
                        ],
                    ]); ?>
                </div>
                <?php Pjax::end(); ?>
            </div>
        </div>
    </div>
</div>
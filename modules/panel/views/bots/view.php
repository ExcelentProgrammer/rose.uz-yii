<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Bots */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Системы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-offset-3 col-md-6">
        <div class="card">
            <div class="header clearfix">
                <h2 class="title"><?= Html::encode($this->title) ?></h2>
            </div>
            <div class="content table-responsive">
                <?= DetailView::widget([
                    'model' => $model,
                    'attributes' => [
                        'id',
                        'name',
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>

<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Bots */

$this->title = 'Добавить';
$this->params['breadcrumbs'][] = ['label' => 'Системы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-offset-3 col-md-6">
        <div class="card clearfix">
            <div class="header">
                <h2 class="title"><?= Html::encode($this->title) ?></h2>
            </div>
            <div class="content table-responsive">
                <?= $this->render('_form', [
                    'model' => $model,
                ]) ?>
            </div>
        </div>
    </div>
</div>
<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Category */

$this->title = 'Добавить';
$this->params['breadcrumbs'][] = ['label' => 'Категории', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
  <div class="header">
    <h2 class="title"><?= Html::encode($this->title) ?></h2>
  </div>
  <hr>
  <div class="content">
    <?= $this->render('_form', [
      'model' => $model,
    ]) ?>
  </div>
</div>
<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Category */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Категории', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="card">
  <div class="header">
    <h2 class="title"><?= Html::encode($this->title) ?></h2>
  </div>
  <hr>
  <div class="content">
    <?= DetailView::widget([
      'model' => $model,
      'attributes' => [
        'id',
        'bot_id',
        'name',
        'name_uz',
        'name_uc',
        'name_en',
        'position',
      ],
    ]) ?>
  </div>
</div>

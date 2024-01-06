<?php

/* @var $this yii\web\View */
/* @var $model app\models\Pages */

use yii\helpers\HtmlPurifier;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('app', 'guarantee');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
  <?= Breadcrumbs::widget([
    'homeLink' => ['label' => Yii::t('app', 'miniTitle'), 'url' => Yii::$app->homeUrl],
    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
  ]) ?>
  <div class="row mb20">
    <div class="left-sidebar">
      <?= $this->render('//catalog/_menu') ?>
    </div>
    <div class="right-sidebar">
      <?= HtmlPurifier::process($model->getText()) ?>
    </div>
  </div>
</div>

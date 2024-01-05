<?php

/* @var $this yii\web\View */
/* @var $model app\models\Pages */

use yii\widgets\Breadcrumbs;
use yii\helpers\HtmlPurifier;

$this->title = Yii::t('app', 'contacts');
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
      <h1 class="page-title"><?=Yii::t('app', 'contacts')?></h1>
      <div class="mb20">
        <iframe
          src="https://yandex.ru/map-widget/v1/?um=constructor%3Acd6f4c1dc9d56f82504bf3a776cb82b2b013227b9324b7cc2df944d9646cd940&amp;source=constructor"
          width="100%" height="400" frameborder="0"></iframe>
      </div>
      <?= HtmlPurifier::process($model->getText()) ?>
    </div>
  </div>
</div>
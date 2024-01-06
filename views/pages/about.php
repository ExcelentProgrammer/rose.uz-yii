<?php

/* @var $this yii\web\View */
/* @var $model app\models\Pages */

use yii\widgets\Breadcrumbs;
use yii\helpers\HtmlPurifier;

$this->title = Yii::t('app', 'about');
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

      <iframe
        src="https://yandex.ru/map-widget/v1/?um=constructor%3Acd6f4c1dc9d56f82504bf3a776cb82b2b013227b9324b7cc2df944d9646cd940&amp;source=constructor"
        width="100%" height="400" frameborder="0"></iframe>

      <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
        <!-- Indicators -->
        <ol class="carousel-indicators">
          <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
          <li data-target="#carousel-example-generic" data-slide-to="1"></li>
        </ol>

        <!-- Wrapper for slides -->
        <div class="carousel-inner" role="listbox">
          <div class="item active text-center">
            <img src="<?= Yii::$app->homeUrl ?>img/shop1.jpg" alt="..." class="img-thumbnail">
          </div>
          <div class="item text-center">
            <img src="<?= Yii::$app->homeUrl ?>img/shop2.jpg" alt="..." class="img-thumbnail">
          </div>
        </div>

        <!-- Controls -->
        <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
          <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
          <span class="sr-only">Previous</span>
        </a>
        <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
          <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
          <span class="sr-only">Next</span>
        </a>
      </div>
    </div>
  </div>
</div>
<?php

use app\models\Category;
use yii\helpers\Html;

$categories = Category::find()->where(['hidden' => 0])->orderBy(['position' => SORT_ASC])->all();
?>
<div class="container-fluid">
  <nav id="w0" class="navbar main-menu" role="navigation">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#w0-collapse"><span
            class="sr-only">Toggle navigation</span> <span class="icon-bar"></span> <span
            class="icon-bar"></span> <span class="icon-bar"></span></button>
        <span class="hidden-sm hidden-md hidden-lg navbar-brand"
              href="#"><?= Yii::t('app', 'miniTitle') ?></span></div>
      <div id="w0-collapse" class="collapse navbar-collapse">
        <ul id="w1" class="navbar-nav nav">
          <?php foreach ($categories as $category): ?>
            <li class="<?= Category::isActive($category->id) ? "active" : "" ?>">
              <?= Html::a($category->getName(), ['catalog/view', 'id' => $category->id]) ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </nav>
</div>
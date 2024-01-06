<?php

use yii\helpers\Html;

$url = Yii::$app->request->get('url');
?>
<ul class="menu-navigation">
  <li>
    <?= Html::a(Yii::t('app', 'about'), ['pages/about'], ['class' => 'page-nav-item ' . (($url === 'about') ? 'page-nav-item-active' : '')]) ?>
  </li>
  <li>
    <?= Html::a(Yii::t('app', 'delivery'), ['pages/delivery'], ['class' => 'page-nav-item ' . (($url === 'delivery') ? 'page-nav-item-active' : '')]) ?>
  </li>
  <li>
    <?= Html::a(Yii::t('app', 'payment'), ['pages/payment'], ['class' => 'page-nav-item ' . (($url === 'payment') ? 'page-nav-item-active' : '')]) ?>
  </li>
  <li>
    <?= Html::a(Yii::t('app', 'guarantee'), ['pages/guarantees'], ['class' => 'page-nav-item ' . (($url === 'guarantees') ? 'page-nav-item-active' : '')]) ?>
  </li>
  <li>
    <?= Html::a(Yii::t('app', 'contacts'), ['pages/contacts'], ['class' => 'page-nav-item ' . (($url === 'contacts') ? 'page-nav-item-active' : '')]) ?>
  </li>
  <li>
    <?= Html::a(Yii::t('app', 'howToOrder'), ['pages/ordering'], ['class' => 'page-nav-item ' . (($url === 'ordering') ? 'page-nav-item-active' : '')]) ?>
  </li>
  <li>
    <?= Html::a(Yii::t('app', 'corp'), ['pages/corp'], ['class' => 'page-nav-item ' . (($url === 'corp') ? 'page-nav-item-active' : '')]) ?>
  </li>
</ul>

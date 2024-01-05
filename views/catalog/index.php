<?php

/* @var $this yii\web\View */

use app\models\Products;
use yii\data\ActiveDataProvider;
use yii\widgets\Breadcrumbs;
use yii\widgets\ListView;

$this->title = Yii::t('app', 'miniTitle');
$this->params['breadcrumbs'][] = $this->title;

$query = Products::find()
  ->leftJoin('product_categories', 'product_categories.product_id=products.id')
  ->leftJoin('category', 'category.id=product_categories.cat_id')
  ->where(['category.bot_id' => Yii::$app->params['system'], 'products.hidden' => 0]);
$dataProvider = new ActiveDataProvider([
  'query' => $query,
  'pagination' => [
    'pageSize' => 12,
  ],
]);

?>
<div class="container mb30">
  <?= Breadcrumbs::widget([
    'homeLink' => ['label' =>  Yii::t('app', 'miniTitle'), 'url' => Yii::$app->homeUrl],
    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
  ]) ?>
  <h1 class="block-title"><?= $this->title ?></h1>
  <?= ListView::widget([
    'dataProvider' => $dataProvider,
    'summary' => false,
    'itemView' => '_list-item',
    'options' => [
      'tag' => false,
    ],
    'itemOptions' => [
      'class' => 'col-xs-12 col-sm-4 col-md-4 col-lg-4 product-item',
    ],
    'layout' => '<div class="row products-list">{items}</div><div class="products-list-pagination">{pager}</div>',
  ]); ?>
</div>
<?= $this->render('//site/info-block') ?>

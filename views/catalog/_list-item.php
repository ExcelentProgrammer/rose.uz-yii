<?php

/* @var $this yii\web\View */

/* @var $model app\models\Products */

use app\models\Orders;

$price = $model->getPrice();

?>
<div class="product-wrapper">
  <div class="product-wrapper-inner">
    <img class="product-photo img-responsive"
         src="<?= Yii::$app->params['imgUrl'] ?>/uploads/products/<?= $model->photo ?>">
    <div class="product-title"><?= $model->getName() ?></div>
    <div class="product-price">
      <span class="product-price-value"><?= number_format($price, 0, '.', ' ') ?></span>
      <span class="product-price-currency"><?= Orders::getCurrencyText() ?></span>
    </div>
    <div class="product-order-block">
      <a class="product-order-btn" href="#" data-act="add-to-cart"
         data-id="<?= $model->id ?>"><?= Yii::t('app', 'addToCart') ?></a>
    </div>
  </div>
</div>
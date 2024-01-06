<?php

use app\models\Dashboard;
use app\models\Orders;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $order Orders */

$cartCount = 0;
$cartTotal = 0;
$cookies = Yii::$app->request->cookies;

if (($cookie = $cookies->get('orderId')) !== null) {
  $order = Orders::find()->where(['id' => $cookie->value, 'state' => [Orders::STATE_TRASH, Orders::STATE_CREATED]])->one();
  if ($order !== null) {
    $cartCount = $order->getItemsCount();
    $cartTotal = $order->getOrderTotalPrice();
  } else {
    Yii::$app->response->cookies->remove('orderId');
  }
}

$cartCount = Orders::getCachedOrderItemCount();
$cartTotal = Orders::getCachedOrderTotalPrice();

$acc = "fa-money";
$currency = Yii::$app->session->get('currency');
if (empty($currency)) {
  $currency = Orders::CUR_SUM;
}

if ($currency == Orders::CUR_RUB)
  $acc = "fa-rub";
elseif ($currency == Orders::CUR_USD)
  $acc = "fa-usd";

$lang = (string)Yii::$app->language;
?>
<div class="container">
  <div class="row upper-menu hidden-xs">
    <div class="col-sm-9 col-md-7 inner-upper-menu">
      <?= Html::a(Yii::t('app', 'about'), ['pages/about']) ?>
      <?= Html::a(Yii::t('app', 'delivery'), ['pages/delivery']) ?>
      <?= Html::a(Yii::t('app', 'payment'), ['pages/payment']) ?>
      <?= Html::a(Yii::t('app', 'guarantee'), ['pages/guarantees']) ?>
      <?= Html::a(Yii::t('app', 'corp'), ['pages/corp']) ?>
      <?= Html::a(Yii::t('app', 'contacts'), ['pages/contacts']) ?>
    </div>
    <div class="col-sm-3 col-md-5">
      <div class="text-right">
        <div class="dropdown">
          <a class="upper-right-menu" href="<?= $lang ?>"><?= Dashboard::$languages[$lang] ?></a>
          <div class="dropdown-content">
            <?php foreach (Dashboard::$languages as $key => $row): ?>
              <?php if ($key === $lang) continue ?>
              <a href="<?= Url::to(['site/language', 'lang' => $key]) ?>"><?= $row ?></a>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="dropdown">
          <a class="upper-right-menu" href="#" title="Валюта"><span class="fa <?= $acc ?> cp"></span><span
              class="hidden-xs hidden-sm pl5"><?= Orders::$currency[$currency] ?></span></a>
          <div class="dropdown-content">
            <?php if ($currency != Orders::CUR_SUM): ?>
              <a href="<?= Url::to(['site/set-currency', 'id' => Orders::CUR_SUM]) ?>"><span
                  class="fa fa-money cp"></span><span
                  class="hidden-xs hidden-sm pl5">СУМ</span></a>
            <?php endif; ?>
            <?php if ($currency != Orders::CUR_RUB): ?>
              <a href="<?= Url::to(['site/set-currency', 'id' => Orders::CUR_RUB]) ?>"><span
                  class="fa fa-rub cp"></span><span
                  class="hidden-xs hidden-sm pl5">РУБ</span></a>
            <?php endif; ?>
            <?php if ($currency != Orders::CUR_USD): ?>
              <a href="<?= Url::to(['site/set-currency', 'id' => Orders::CUR_USD]) ?>"><span
                  class="fa fa-usd cp"></span><span
                  class="hidden-xs hidden-sm pl5">USD</span></a>
            <?php endif; ?>
          </div>
        </div>
        <a class="upper-right-menu rp0" href="<?= Url::to('state') ?>" title="Статус заказа"><span
            class="fa fa-check cp"></span><span
            class="hidden-xs hidden-sm pl5"><?= Yii::t('app', 'orderStatus') ?></span></a>
      </div>
    </div>
  </div>
  <div class="row logo-block">
    <div class="col-xs-6 col-sm-6 col-md-4 col-lg-3 logo">
      <a href="<?= Yii::$app->homeUrl ?>"
         title="<?= Yii::t('app', 'title') ?>">
        <div class="pull-left"><img class="img-responsive" alt="Rose.uz"
                                    src="<?= Yii::$app->homeUrl ?>img/logo.png"></div>
      </a>
    </div>
    <div class="hidden-xs hidden-sm hidden-md col-sm-4 col-lg-4 info-block">
      <div class="gtc"><?= Yii::t('app', 'address') ?></div>
      <div class="logo-block-lg-text">
        <a href="https://yandex.uz/maps/-/CCauRU~u" target="_blank" style="color:#3f3f3f">
          <i class="fa fa-map-marker fa-lg cp"></i><?= Yii::t('app', 'addressValue') ?>
        </a>
      </div>
    </div>
    <div class="hidden hidden-xs hidden-sm hidden-md col-sm-3 col-md-4 col-lg-1 info-block">
      <div class="gtc"><?= Yii::t('app', 'phone') ?></div>
      <div class="logo-block-lg-text">+998 71 200-9800</div>
    </div>
    <div class="hidden-xs hidden-sm col-sm-offset-3 col-md-offset-4 col-lg-offset-1 col-md-4 col-lg-2 info-block">
      <div class="gtc"><?= Yii::t('app', 'office') ?></div>
      <div class="logo-block-lg-text">+998 71 200-9800</div>
    </div>
    <div class="col-xs-6 col-sm-6 col-md-4 col-lg-2 shopping-cart">
      <div class="text-right">
        <a href="<?= Url::to(['/cart']) ?>">
          <div class="cart-icon">
            <span id="cart-badge" class="badge"><?= $cartCount ?></span><i
              class="fa fa-shopping-basket fa-lg cp"></i>
          </div>
          <div class="shopping-cart-price"><span
              class="hidden-xs cart-price"><?= $cartTotal ?></span> <span
              class="hidden-xs cart-currency"><?= Orders::getCurrencyText() ?></span>
          </div>
        </a>
      </div>
    </div>
  </div>
  <div id="cart-block" class="cart-btn-area <?= ($cartTotal == 0) ? 'hidden' : '' ?>">
    <div class="cart-btn-container">
      <a href="<?= Url::to(['/cart']) ?>" class="cart-btn"><i class="fa fa-shopping-basket fa-lg"></i></a>
    </div>
  </div>
</div>
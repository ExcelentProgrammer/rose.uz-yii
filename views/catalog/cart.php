<?php

/* @var $this yii\web\View */
/* @var $model app\models\Orders */

/* @var $gifts app\models\Products[] */

use app\models\Orders;
use app\models\Products;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('app', 'cart');
$this->params['breadcrumbs'][] = $this->title;
$currencyText = Orders::getCurrencyText();
$cachedOrder = Orders::getCachedOrder();
?>
<div class="container hidden">
  <?= Breadcrumbs::widget([
    'homeLink' => ['label' => Yii::t('app', 'miniTitle'), 'url' => Yii::$app->homeUrl],
    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
  ]) ?>
  <div class="alert alert-danger mb30">
    <p>Уважаемые клиенты, к сожалению в данное время Вы не можете порадовать своих близких. <br>Приём заявок
      остановлен, в связи с нагрузкой сегодняшнего праздника. <br>Мы не хотим Вас подводить, поэтому приём
      заявок откроется по окночанию всех принятых заявок. Надеемся на Ваше понимание. <br>Мы работаем над
      собой, и будем стремиться не приостанавливать сервис.</p>
  </div>
</div>

<div class="container mb30">
  <?= Breadcrumbs::widget([
    'homeLink' => ['label' => Yii::t('app', 'miniTitle'), 'url' => Yii::$app->homeUrl],
    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
  ]) ?>
  
  <div>
    <?php if (count($cachedOrder['items']) == 0): ?>
        <div class="alert alert-danger">
        <p><?= Yii::t('app', 'cartIsEmpty') ?></p>
        </div>
    <?php else: ?>
        <?php $form = ActiveForm::begin([
        'id' => 'cart-form',
        ]); ?>
        <div class="row line-cop-area mb20">
        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <div class="cart-line">
            <div class="line">
                <div class="line-part active">
                <div class="line-part-wrapper">
                    <div class="line-part-title"><?= Yii::t('app', 'cart') ?></div>
                    <div class="line-part-circle">1</div>
                </div>
                </div>
                <div class="line-part" style="text-align: center">
                <div class="line-part-wrapper">
                    <div class="line-part-title" style="left:-80px;color:#d5d5d5"><?= Yii::t('app', 'checkout') ?>
                    </div>
                    <div class="line-part-circle" style="background: #fff; color: #000">2</div>
                </div>
                </div>
                <div class="line-part" style="text-align: right">
                <div class="line-part-wrapper">
                    <div class="line-part-title" style="color:#d5d5d5"><?= Yii::t('app', 'payment') ?></div>
                    <div class="line-part-circle" style="background: #fff; color: #000">3</div>
                </div>
                </div>
            </div>
            </div>
        </div>
        </div>
        <div class="cart-items mb30">
        <table class="table cart-table">
            <tbody>
            <?php
            $total = 0;
            foreach ($cachedOrder['items'] as $item):
            $product = Products::findOne($item['id']);
            $itemPrice = Orders::getAbsolutePrice((float)$product->price * (int)$item['quantity']);
            $total += $itemPrice;
            ?>
            <tr data-id="<?= $item['id'] ?>" data-quantity="<?= $item['quantity'] ?>"
                data-price="<?= $product->price ?>">
                <td class="cart-item-remove"><a href="#" data-act="delete-item"><span
                    class="fa fa-trash-o"></span></a>
                </td>
                <td class="cart-item-image"><img class="img-responsive"
                                                src="<?= Yii::$app->params['imgUrl'] ?>/uploads/products/<?= $product->photo ?>">
                </td>
                <td class="cart-item-desc"><?= $product->name ?></td>
                <td class="cart-item-quantity">
                <div class="cart-item-quantity-area">
                            <span class="quantity-minus <?= ($item['quantity'] == 1) ? 'op3' : '' ?>"
                                data-act="minus-quantity">
                                <img src="<?= Yii::$app->homeUrl ?>img/cart_minus.png">
                            </span>
                    <input type="text" class="quantity-field" value="<?= $item['quantity'] ?>">
                    <span class="quantity-plus" data-act="plus-quantity">
                                <img src="<?= Yii::$app->homeUrl ?>img/cart_plus.png">
                            </span>
                </div>
                </td>
                <td class="cart-item-price">
                <span class="uc-price"><?= number_format($itemPrice, 0, '.', ' '); ?></span>
                <span class="cur-price"><?= $currencyText ?></span>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if ($total > 0): ?>
            <tr class="cart-total-area">
                <td colspan="3"></td>
                <td colspan="2">
                <div class="col-md-6 text-left p0">
                    <span class="total-title"><?= Yii::t('app', 'totalPosition') ?></span>
                </div>
                <div class="col-md-6 text-right p0" style="line-height: 40px">
                    <span class="uc-price position-total"><?= number_format($total, 0, '.', ' ') ?></span>
                    <span class="cur-price"><?= $currencyText ?></span>
                </div>
                </td>
            </tr>
            <tr class="cart-total-area">
                <td colspan="3"></td>
                <td colspan="2">
                <div class="col-md-6 text-left p0">
                    <span class="total-title"><?= Yii::t('app', 'delivery') ?>:</span>
                </div>
                <div class="col-md-6 text-right p0" style="line-height: 40px">
                                <span class="uc-price delivery-price"
                                    data-delivery-price="0">0</span>
                    <span class="cur-price"><?= $currencyText ?></span>
                </div>
                </td>
            </tr>
            <tr class="cart-total-area">
                <td colspan="3"></td>
                <td colspan="2">
                <div class="col-md-6 text-left p0">
                    <span class="total-title"><?= Yii::t('app', 'total') ?></span>
                </div>
                <div class="col-md-6 text-right p0" style="font-size: 25px">
                    <span class="uc-price total"><?= number_format($total, 0, '.', ' ') ?></span>
                    <span class="cur-price"><?= $currencyText ?></span>
                </div>
                </td>
            </tr>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
        <div class="order-options">
        <div class="row">
            <div class="col-md-4">
            <span class="fa fa-envelope-open-o fa-lg cp"></span>
            <div id="add-card" class="checkbox">
                <label><?= Html::checkbox('Orders[add_card]', $cachedOrder['add_card'] == '1' ? true : false) ?>
                <span class="cr"><i
                    class="cr-icon fa fa-check"></i></span> <?= Yii::t('app', 'addCard') ?>
                <strong><em><?= Yii::t('app', 'free') ?>!</em></strong></label></div>
            </div>
            <div class="col-md-4">
            <span class="fa fa-camera-retro fa-lg cp"></span>
            <div class="checkbox">
                <label><?= Html::checkbox('Orders[take_photo]', $cachedOrder['take_photo'] == '1' ? true : false) ?>
                <span
                    class="cr"><i
                    class="cr-icon fa fa-check"></i></span> <?= Yii::t('app', 'shotPhoto') ?></label>
            </div>
            </div>
        </div>
        <div class="col-md-12 order-options-detail <?= !$cachedOrder['add_card'] ? 'dpn' : '' ?>">
            <div class="title"
                style="margin-top: 15px; margin-bottom: 10px; font-size: 14px; color: black"><?= Yii::t('app', 'greetings') ?></div>
            <?= $form->field($model, 'card_text')->textarea(['class' => 'form-control', 'rows' => 5, 'style' => 'border: 0; border-radius: 0; margin-bottom: 10px', 'placeholder' => 'Введите текст для открытки', 'value' => $cachedOrder['card_text']])->label(false) ?>
        </div>
        </div>

        <div style="margin-bottom: 50px">
        <div class="hidden-xs text-center mb20"><?= Yii::t('app', 'acceptOffer') ?>
        </div>
        <div class="text-center mb20">
            <?= Html::submitButton('Оформить заказ', ['class' => 'order-state-btn']) ?>
        </div>
        </div>

        <div class="mb30 hidden">
        <div class="row">
            <div class="col-sm-12 col-md-12 col-lg-12">
            <h1 class="page-title" style="text-align: center; font-weight: 300"><?= Yii::t('app', 'addToBouquet') ?></h1>
            </div>
        </div>
        <div class="gifts row">
            <?php $gifts = Products::find()->leftJoin('product_categories', 'products.id=product_categories.product_id')->where(['product_categories.cat_id' => 13])->limit(12)->all(); ?>
            <?php foreach ($gifts as $gift): ?>
            <div class="col-xs-12 col-sm-6 col-md-2 col-lg-2 p0">
                <div class="gift-item">
                <div class="gift-img">
                    <img src="<?= Yii::$app->params['imgUrl'] ?>/uploads/products/<?= $gift->photo ?>">
                </div>
                <div class="gift-item-title"><?= $gift->name ?></div>
                <div class="gift-item-price text-center">
                    <span class="uc-price"><?= number_format($gift->getPrice(), 0, '.', ' ') ?></span>
                    <span class="cur-price"><?= $currencyText ?></span>
                </div>
                <div class="gift-add text-center">
                    <button class="gift-add-btn" data-act="add-gift" data-id="<?= $gift->id ?>">
                    <span class="add-btn-plus">+</span>
                    <?= Yii::t('app', 'add') ?>
                    </button>
                </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mb20">
            <?= Html::submitButton(Yii::t('app', 'checkingOut'), ['class' => 'order-state-btn']) ?>
        </div>
        </div>

        <?php ActiveForm::end(); ?>
    <?php endif; ?>
  </div>
</div>

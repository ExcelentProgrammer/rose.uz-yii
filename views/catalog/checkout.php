<?php

/* @var $this yii\web\View */

/* @var $model app\models\Orders */

use app\models\Orders;
use app\models\Products;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

$this->title = 'Оформление заказа на сайте Rose.uz';
$model->scenario = "checkout";
$currencyText = Orders::getCurrencyText();
$cachedOrder = Orders::getCachedOrder();
?>
<div class="container mb30">
  <div class="row line-cop-area mb30">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
      <div class="cart-line">
        <div class="line">
          <div class="line-part">
            <div class="line-part-wrapper">
              <div class="line-part-title"><?= Yii::t('app', 'cart') ?></div>
              <div class="line-part-circle">1</div>
            </div>
          </div>
          <div class="line-part active" style="text-align: center">
            <div class="line-part-wrapper">
              <div class="line-part-title" style="left:-80px;"><?= Yii::t('app', 'checkout') ?></div>
              <div class="line-part-circle">2</div>
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

  <div class="row checkout-goods-title">
    <div class="din">
      <span
        style="font-weight:400;font-size:20px;text-transform:uppercase;letter-spacing:3px;color:#000;"><?= Yii::t('app', 'yourOrder') ?></span>
      <span
        style="font-style:italic;font-size:15px;color:#a3a3a3; padding-right: 10px"> <?= Yii::t('app', 'withDelivery') ?>: <span
          class="hidden-xs"></span></span>
    </div>
    <div class="din price-tag">
      <span class="uc-price"><?= Orders::getCachedOrderTotalPrice() ?></span>
      <span class="cur-price"><?= $currencyText ?></span>
    </div>
  </div>
  <div class="row checkout-goods mb20">
    <?php foreach ($cachedOrder['items'] as $item):
      $product = Products::findOne($item['id']);
      $price = Orders::getAbsolutePrice($product->price * $item['quantity']) ?>
      <div class="col-md-4 col-lg-4 checkout-goods-item clearfix">
        <div class="checkout-goods-img">
          <img src="<?= Yii::$app->params['imgUrl'] ?>/uploads/products/<?= $product->photo ?>">
        </div>
        <div class="checkout-goods-detail">
          <div class="checkout-goods-item-title"><?= $product->name ?></div>
          <div class="checkout-goods-item-price price-tag">
            <span class="uc-price"><?= number_format($price, 0, '.', ' ') ?></span>
            <span class="cur-price"><?= $currencyText ?></span>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <?php $form = ActiveForm::begin([
    'id' => 'checkout-form',
  ]); ?>
  <?= $form->errorSummary($model) ?>
  <div class="order-options" style="margin-bottom: 5px">
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
                class="cr-icon fa fa-check"></i></span> <?= Yii::t('app', 'shotPhoto') ?></label></div>
      </div>
    </div>
    <div class="col-md-12 order-options-detail <?= !$cachedOrder['add_card'] ? 'dpn' : '' ?>">
      <div class="title"
           style="margin-top: 15px; margin-bottom: 10px; font-size: 14px; color: black"><?= Yii::t('app', 'greetings') ?></div>
      <?= $form->field($model, 'card_text')->textarea(['class' => 'form-control', 'rows' => 5, 'style' => 'border: 0; border-radius: 0; margin-bottom: 10px', 'placeholder' => 'Введите текст для открытки', 'value' => $cachedOrder['card_text']])->label(false) ?>
    </div>
  </div>
  <div class="checkout-details-area mb20">
    <h3 class="checkout-detail-title">Отправитель</h3>
    <div class="row">
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 checkout-detail-field">
        <?= $form->field($model, 'sender_name')->textInput(['placeholder' => Yii::t('app', 'yourName'), 'value' => $cachedOrder['sender_name']])->label(false) ?>
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 checkout-detail-field">
        <?= $form->field($model, 'sender_phone')->textInput(['placeholder' => Yii::t('app', 'yourPhone'), 'value' => $cachedOrder['sender_phone']])->label(false) ?>
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 checkout-detail-field">
        <?= $form->field($model, 'sender_email')->textInput(['placeholder' => Yii::t('app', 'yourEmail'), 'value' => $cachedOrder['sender_email']])->label(false) ?>
      </div>
    </div>
    <h3 class="checkout-detail-title">Получатель</h3>
    <div class="row">
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 checkout-detail-field">
        <?= $form->field($model, 'receiver_name')->textInput(['placeholder' => Yii::t('app', 'fullname'), 'value' => $cachedOrder['receiver_name']])->label(false) ?>
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 checkout-detail-field">
        <?= $form->field($model, 'receiver_phone')->textInput(['placeholder' => Yii::t('app', 'receiverPhone'), 'value' => $cachedOrder['receiver_phone']])->label(false) ?>
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 checkout-detail-field">
        <?= $form->field($model, 'delivery_date')->widget(DatePicker::classname(), [
          'options' => [
            'placeholder' => Yii::t('app', 'deliveryDate'),
            'autocomplete' => 'off'
          ],
          'type' => DatePicker::TYPE_INPUT,
          'pluginOptions' => [
            'autoclose' => true,
            'format' => 'dd.mm.yyyy',
            "startDate" => "09.03.2019",
            'todayHighlight' => true
          ]
        ])->label(false) ?>
      </div>
    </div>
    <h3 class="checkout-detail-title"><?= Yii::t('app', 'deliveryTime') ?></h3>
    <?= $form->field($model, 'delivery_period')->radioList(Orders::$period)->label(false) ?>

    <h3 class="checkout-detail-title"><?= Yii::t('app', 'deliveryAddress') ?></h3>
    <div class="row mb20">
      <div class="col-xs-12 col-sm-8 col-md-8 col-lg-8 checkout-detail-field">
        <?= $form->field($model, 'receiver_address')->textarea(['class' => 'form-control address-field', 'placeholder' => Yii::t('app', 'enterDeliveryAddress'), 'rows' => 5, 'value' => $cachedOrder['receiver_address']])->label(false) ?>
      </div>
      <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 checkout-detail-field">
        <div class="checkbox"><label
            style="padding-left: 0"><?= Html::checkbox('Orders[know_address]', $cachedOrder['know_address'] == '1' ? true : false) ?>
            <span
              class="cr"><i
                class="cr-icon fa fa-check"></i></span> <?= Yii::t('app', 'knowReceiver') ?></label>
        </div>
      </div>
    </div>
  </div>
  <div class="text-center mb20">
    <?= Html::submitButton(Yii::t('app', 'goToPayment'), ['class' => 'order-state-btn']) ?>
  </div>
  <?php ActiveForm::end(); ?>
</div>

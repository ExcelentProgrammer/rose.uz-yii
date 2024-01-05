<?php

use app\models\Click;
use app\models\Orders;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Orders */
/* @var $system string */

$this->title = Yii::t('app', 'onlinePayment');
$totalPrice = $model->getOrderTotalSumPrice();
$currencyText = Orders::getCurrencyText();
$totalUsd = $model->getOrderTotalUsdPrice();

$sucHash = hash('sha256', "Online payment for order: " . $model->id);
$comment = "Оплата на сайте Rose.uz. Номер заказа: " . $model->id;
$commentUrl = urlencode($comment);

switch ($system) {
  case 'qiwi':
    Yii::$app->response->redirect("https://bill.qiwi.com/order/external/create.action?txn_id={$model->id}&comm={$commentUrl}&from=584814&summ={$totalUsd}&currency=USD");
    break;
  case "payme":
    $this->registerJs("$('#pm').submit()");
    break;
  case "click":
    $this->registerJs("$('#ck').submit()");
    break;
  case "yandex":
    $this->registerJs("$('#ym').submit()");
    break;
  case "yandex-visa":
    $this->registerJs("$('#ym-v').submit()");
    break;
  case "web-money":
    $this->registerJs("$('#wm').submit()");
    break;
}
?>
<div class="row" style="padding-bottom: 40px; display: none">
  <form id="pm" method="POST" action="https://checkout.paycom.uz" class="ng-pristine ng-valid">
    <input type="hidden" name="merchant" value="<?= \app\models\PaymeTransaction::MERCHANT_ID ?>">
    <input type="hidden" name="amount" class="payment-amount payment-amount-payme"
           value="<?= $totalPrice * 100 ?>">
    <input type="hidden" name="account[order_id]" value="<?= $model->id ?>">
    <input type="hidden" name="lang" value="ru"/>
    <input type="hidden" name="description" value="Оплата на сайте Rose.uz"/>
    <button type="submit" name="type" value="payme" class="payment-btn">
      <div class="payment-system payment-payme">
        <small>Payme</small>
      </div>
    </button>
  </form>

  <?php $click = new Click($model->id); ?>
  <form id="ck" action="https://my.click.uz/pay/" method="post">
    <input type="hidden" name="MERCHANT_ID" value="<?= Click::MERCHANT_ID ?>"/>
    <input type="hidden" name="MERCHANT_USER_ID" value="<?= Click::MERCHANT_USER_ID ?>"/>
    <input type="hidden" name="MERCHANT_SERVICE_ID" value="<?= Click::SERVICE_ID ?>"/>
    <input type="hidden" name="MERCHANT_TRANS_ID" value="<?= $click->transId ?>"/>
      <input type="hidden" name="MERCHANT_TRANS_NOTE" value=""/>
    <input type="hidden" name="MERCHANT_USER_PHONE" value=""/>
    <input type="hidden" name="MERCHANT_USER_EMAIL" value=""/>
    <input type="hidden" name="SIGN_TIME" value="<?= $click->date ?>"/>
    <input type="hidden" name="SIGN_STRING" value="<?= $click->signString ?>"/>
    <input type="hidden" name="MERCHANT_TRANS_NOTE_BASE64" value=""/>
    <input type="hidden" name="MERCHANT_TRANS_AMOUNT" value="<?= $totalPrice ?>">
    <button type="submit" class="payment-btn">
      <div class="payment-system payment-click">
        <small>Click</small>
      </div>
    </button>
  </form>

  <form id="ym" method="POST" action="https://money.yandex.ru/quickpay/confirm.xml">
    <input type="hidden" name="receiver" value="410011484142953">
    <input type="hidden" name="formcomment" value="Оплата на сайте Rose.uz">
    <input type="hidden" name="short-dest"
           value="Оплата за заказа: <?= $model->id ?>">
    <input type="hidden" name="label" value="$order_id">
    <input type="hidden" name="quickpay-form" value="shop">
    <input type="hidden" name="targets" value="транзакция ID: <?= $model->id ?>">
    <input type="hidden" name="sum" value="<?= $model->getOrderTotalRubPrice() ?>" data-type="number">
    <input type="hidden" name="comment" value="<?= $comment ?>">
    <input type="hidden" name="successURL"
           value="http://rose.uz/site/complete?system=wm">
    <button type="submit" class="payment-btn">
      <div class="payment-system payment-ym">
        <small>Yandex Money</small>
      </div>
    </button>
  </form>

  <form id="ym-v" method="POST" action="https://money.yandex.ru/quickpay/confirm.xml">
    <input type="hidden" name="receiver" value="410011484142953">
    <input type="hidden" name="formcomment" value="Оплата на сайте Rose.uz">
    <input type="hidden" name="short-dest"
           value="Оплата за заказа: <?= $model->id ?>">
    <input type="hidden" name="label" value="$order_id">
    <input type="hidden" name="quickpay-form" value="shop">
    <input type="hidden" name="targets" value="транзакция ID: <?= $model->id ?>">
    <input type="hidden" name="sum" value="<?= $model->getOrderTotalRubPrice() ?>" data-type="number">
    <input type="hidden" name="comment" value="<?= $comment ?>">
    <input type="hidden" name="paymentType" value="AC">
    <input type="hidden" name="successURL"
           value="http://rose.uz/site/complete?system=wm">
    <button type="submit" class="payment-btn">
      <div class="payment-system payment-vmc">
        <small>Visa & Master Card</small>
      </div>
    </button>
  </form>

  <form id="wm" method="POST" action="https://merchant.webmoney.ru/lmi/payment.asp" accept-charset="windows-1251">
    <input type="hidden" name="LMI_PAYMENT_AMOUNT" value="<?= $model->getOrderTotalUsdPrice() ?>">
    <input type="hidden" name="LMI_PAYMENT_DESC" value="Оплата на сайте Rose.uz">
    <input type="hidden" name="LMI_PAYMENT_NO" value="<?= $model->id ?>">
    <input type="hidden" name="LMI_PAYEE_PURSE" value="Z118616263141">
    <input type="hidden" name="order_id" value="<?= $model->id ?>">
    <button type="submit" class="payment-btn">
      <div class="payment-system payment-wm">
        <small>Web Money</small>
      </div>
    </button>
  </form>
</div>
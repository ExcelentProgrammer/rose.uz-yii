<?php

/* @var $this yii\web\View */

/* @var $model app\models\Orders */

use app\models\Click;
use app\models\Dashboard;
use app\models\Orders;
use app\models\PaymeTransaction;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = Yii::t('app', 'onlinePayment');
$totalPrice = $model->getOrderTotalSumPrice();
$currencyText = Orders::getCurrencyText();

$sucHash = hash('sha256', "Online payment for order: " . $model->id);
$comment = "Оплата на сайте Rose.uz. Номер заказа: " . $model->id;
$commentUrl = urlencode($comment);

$total = $totalPrice * 100;
$p = "m=" . PaymeTransaction::MERCHANT_ID . ";ac.order_id={$model->id};a={$total};l=ru;c=" . Url::to(['catalog/complete'], true);
$url = "https://checkout.paycom.uz/" . base64_encode($p);
$systemOutage = false;
$outageMessage = "Для качественной работы, на сегодня мы больше не принимаем заказы спасибо большое за то что вы выбрали нас";

/*$descr = "";
foreach ($model->items as $item) {
    $descr .= $item->product_name. "\n" . $descr;
}*/
?>

<?php if ($systemOutage): ?>

    <div class="d-flex w-100 mb-30" style="height: 400px; justify-content: center; align-items: center; display: flex;">
        <span class="text-muted"><?= $outageMessage ?></span>
    </div>

<?php else: ?>
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
          <div class="line-part" style="text-align: center">
            <div class="line-part-wrapper">
              <div class="line-part-title" style="left:-80px;"><?= Yii::t('app', 'checkout') ?></div>
              <div class="line-part-circle">2</div>
            </div>
          </div>
          <div class="line-part active" style="text-align: right">
            <div class="line-part-wrapper">
              <div class="line-part-title"><?= Yii::t('app', 'payment') ?></div>
              <div class="line-part-circle">3</div>
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
      <span class="line-gradient"
            style="padding:1px;display:inline-block;margin-left:13px;margin-right:13px;"><span
          style="background:#f7f7f7;width:100%;height:100%;padding:8px 10px 8px 10px;display:inline-block;color:#000;font-size:15px;font-weight:600;">№ <?= $model->id ?></span></span>
      <span
        style="font-style:italic;font-size:15px;color:#a3a3a3; padding-right: 10px"> <?= Yii::t('app', 'withDelivery') ?>: </span>
    </div>
    <div class="din price-tag">
      <span class="uc-price"><?= $model->getOrderTotalPrice() ?></span>
      <span class="cur-price"><?= $currencyText ?></span>
    </div>
    <div class="din">
      <span
        style="font-style:italic;font-size:15px;color:#a3a3a3; padding-right: 10px"> <?= Yii::t('app', 'acceptedAndPay') ?> </span>
    </div>
  </div>

  <div class="row" style="padding-bottom: 40px;">
    <a href="<?= $url ?>" type="submit" name="type" value="payme" class="payment-btn">
      <div class="payment-system payment-payme">
        <small>Payme</small>
      </div>
    </a>

    <?php $click = new Click($model->id); ?>
    <form id="click_form" action="https://my.click.uz/pay/" method="post">
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

    <a
      href="https://bill.qiwi.com/order/external/create.action?comm=<?= $commentUrl ?>&from=584814&summ=<?= $model->getOrderTotalUsdPrice() ?>&currency=USD"
      type="submit" class="payment-btn">
      <div class="payment-system payment-qiwi">
        <small>Qiwi Кошелек</small>
      </div>
    </a>


      <button class="payment-btn paybox-btn">
        <div class="payment-system payment-vmc">
          <small>Visa & Master Card</small>
        </div>
      </button>

    <form method="POST" action="https://merchant.webmoney.ru/lmi/payment.asp" accept-charset="windows-1251">
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

    <a href="<?= Url::to(['catalog/complete']) ?>" type="button" class="payment-btn">
      <div class="payment-system payment-cash">
        <small>При получении</small>
      </div>
    </a>
  </div>
  <div class="alert alert-danger hidden">К сожалению данный момент мы не можем принимать оплаты!</div>
</div>
<div id="paynetModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header" style="background: #EE3963;color: white">
        <h4 class="modal-title" style="text-align: center">Оплата через PAYNET</h4>
      </div>
      <div class="modal-body">
        <div class="callback-modal-container">
          <div class="row">
            <div class="col-md-offset-2 col-md-8 paynet-text mb20">
              <p>Чтобы оплатить через платёжную систему <strong>PAYNET</strong>, найдите ближайший пункт
                оплаты, в разделе <strong>Сервусы или Услуги</strong> найдите поставщика <strong>Rose.uz</strong>
                введите <br>ID: <strong><?= $model->id ?></strong> и сумму заказа:
                <strong><?= number_format($totalPrice, 0, '', ' ') ?> сум</strong></p>
              <button class="btn btn-info" data-dismiss="modal">OK</button>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>
</div>
<?php endif; ?>



<?php
$js = <<< JS
  (function (p, a, y, b, o, x) {
  o = p.createElement(a);
  x = p.getElementsByTagName(a)[0];
  o.async = 1;
  o.src = 'https://widget.paybox.money/v1/paybox/pbwidget.js?' + 1 * new Date();
  x.parentNode.insertBefore(o, x);
})(document, 'script');
JS;
//$descr
$payboxCurrenc = "UZS";

switch ($currencyText) {
    case "РУБ":
        $payboxCurrenc = "RUB";
        break;
    
    case "СУМ":
        $payboxCurrenc = "UZS";
        break;
    
    case "USD":
        $payboxCurrenc = "USD";
        break;
}
// if ($currencyText == "РУБ"){
//     $payboxCurrenc = "RUB";
// } else if ($currencyText == "USD") {
//     $payboxCurrenc = $currencyText;
// }

$pay = <<< JS
function pay(amount) {
  var orderId = parseInt({$model->id})
  console.log('ord', orderId)
  console.log("CurrencyText", `{$currencyText}`)
var data = {
  token: 'irBJaWJSym46vFqtUYUcx4GmxZSKHySw',
  payment: {
    order: `{$model->id}`,
    amount: amount,
    currency: `{$payboxCurrenc}`,
    language: 'ru',
    description: `Заказ на сайтe Rose.uz`,
    //test: 1,  // testing mode
    options: {
      callbacks: {
        result_url: 'https://rose.uz/site/complete?system=pb'
        //check_url: 'https://rose.uz/site/check'
      },

      custom_params: {},
      user: {
        phone: '{$model->sender_phone}'
      },
      /*receipt_positions: [
        {
          count: 2,
          name: 'Коврик для мыши',
          tax_type: 3,
          price: amount
        }
      ]*/

    }
  },
  successCallback: function (payment) {
    //alert('Заказ номер ' + payment + ' успешно оплачен')
    console.log('payment:', payment)
    $.ajax({
        url: "ajax/paybox-complete",
        type: 'POST',
        data: {
            //phone: payment.options.user.phone,
            //email: payment.options.user.email,
            //amount: payment.amount,
            //description: payment.description,
            order: payment.order,
            delivery: `{$model->delivery_date}`
           
        }
    })
    window.location.href = "https://rose.uz/site/complete?system=pb"
  },
  errorCallback: function (payment) {
    alert('Произошла ошибка при попытке оплаты заказа номер ' + payment.order)
    console.log('payment: ' + payment)
  }
}

var paybox = new PayBox(data);
paybox.create();
}


$('.paybox-btn').click(function() {
  var amount = '{$model->getOrderTotalPrice()}'.replaceAll(" ", "")
  amount = parseInt(amount)
  console.log('ettttt', amount)
  pay(amount)
})

$('#one').click(function() {
    
  //window.location.href = "https://rose.uz/site/complete?system=pb"
})
JS;

$this->registerJs($js); 

$this->registerJs($pay); 
<?php

/* @var $this yii\web\View */
/* @var $model app\models\Chats */

$this->title = 'Payme';
?>
<div class="logo-block">
    <img class="payme-logo" src="<?= Yii::$app->homeUrl ?>images/logo.png">
</div>
<div class="col-md-4 payment-block">
    <form id="payme-form" method="POST" action="https://checkout.paycom.uz" class="ng-pristine ng-valid">
        <input type="hidden" name="merchant" value="<?= \app\models\PaymeTransaction::MERCHANT_ID ?>">
        <input type="hidden" name="amount" class="payment-amount payment-amount-payme">
        <input type="hidden" name="account[order_id]" value="<?= $model->chat_id ?>">
        <div class="form-group">
            <input type="number" id="amount-convert" class="form-control amount-field" placeholder="Sumani kiriting">
        </div>
        <div class="form-group">
            <button class="btn btn-payme">To'lovga o'tish</button>
        </div>
    </form>
</div>

<script type="text/javascript">
    $('#payme-form').submit(function (e) {
        $('[name="amount"]').val($('#amount-convert').val() * 100);
    });
</script>
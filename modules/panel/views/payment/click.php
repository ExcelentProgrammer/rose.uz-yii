<?php
use app\models\Click;

/* @var $this yii\web\View */
/* @var $model app\models\Chats */

$click = new Click($model->chat_id);
$this->title = 'Click';
?>
<div class="logo-block">
    <img class="payme-logo" src="<?= Yii::$app->homeUrl ?>images/logo.png">
</div>
<div class="col-md-4 payment-block">
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
        <div class="form-group">
            <input type="text" name="MERCHANT_TRANS_AMOUNT" value=""
                   class="form-control amount-field" placeholder="Sumani kiriting">
        </div>
        <button class="btn btn-click">To'lovga o'tish</button>
    </form>
</div>
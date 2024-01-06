<?php

use app\models\Click;

/* @var $this yii\web\View */
/* @var $model app\models\Orders */

$this->title = "Click";
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs('$("#click_form").submit();');
?>

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
  <input type="hidden" name="MERCHANT_TRANS_AMOUNT" value="<?= $model->getOrderTotalPrice(true) ?>">
</form>

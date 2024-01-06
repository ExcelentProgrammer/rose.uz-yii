<?php

use app\models\PaymeTransaction;

/* @var $this yii\web\View */
/* @var $model app\models\Orders */

$this->title = "Payme";
$this->params['breadcrumbs'][] = $this->title;
$this->registerJs('$("#payme-form").submit();');
?>
<form id="payme-form" method="POST" action="https://checkout.paycom.uz">
  <input type="hidden" name="merchant" value="<?= PaymeTransaction::MERCHANT_ID ?>">
  <input type="hidden" name="amount" value="<?= $model->total * 100 ?>">
  <input type="hidden" name="account[order_id]" value="<?= $model->id ?>">
  <input type="hidden" name="lang" value="ru"/>
  <input type="hidden" name="description" value="Оплата за услуги ресторана Yaponamama"/>
</form>
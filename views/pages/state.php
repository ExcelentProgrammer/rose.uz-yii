<?php

/* @var $this yii\web\View */
/* @var $order_id integer */

/* @var $model \app\models\Orders */

use app\models\Orders;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('app', 'orderStatus');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container">
  <?= Breadcrumbs::widget([
    'homeLink' => ['label' => Yii::t('app', 'miniTitle'), 'url' => Yii::$app->homeUrl],
    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
  ]) ?>
  <?php $form = ActiveForm::begin(); ?>
  <div class="row mb50">
    <h1 class="page-title">Статус заказа</h1>
    <?php if (!empty($order_id)): ?>
      <?php if ($model !== null): ?>
        <div class="alert alert-info" role="alert">
          Статус вашего заказа [№ <?= $order_id ?>]: <strong><?= Orders::$statuses[$model->state] ?></strong>.
        </div>
      <?php else: ?>
        <div class="alert alert-warning" role="alert">
          Такой заказ не найден!
        </div>
      <?php endif; ?>
    <?php endif; ?>
    <p class="mb20">Чтобы узнать статус заказа и оплатить введите его номер и нажмите <b>Проверить статус заказа</b>.
    </p>

    <div class="order-state-block mb20">
      <input type="text" name="order_id" class="form-control order-state-field" placeholder="Номер заказа"
             value="<?= $order_id ?>">
    </div>
    <div class="order-state-submit-area">
      <button class="order-state-btn" type="submit">Проверить статус заказа</button>
    </div>
  </div>
  <?php ActiveForm::end(); ?>
</div>

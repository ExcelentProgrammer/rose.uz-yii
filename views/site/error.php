<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */

/* @var $exception Exception */

use yii\helpers\Html;

$code = $exception->getCode();
if ($code == 0) {
  $code = $exception->statusCode;
}
$this->title = "Ошибка " . $code;
?>
<div class="row">
  <div class="col-md-12">
    <div class="error-template">
      <h1>
        Oops!</h1>
      <h2><?= Html::encode($this->title) ?></h2>
      <div class="error-details">
        <?= nl2br(Html::encode($exception->getMessage())) ?>
      </div>
      <div class="error-actions">
        <a href="<?= \yii\helpers\Url::to("/", true) ?>" class="btn btn-primary btn-lg"><span
            class="glyphicon glyphicon-home"></span>
          Назад </a>
      </div>
    </div>
  </div>
</div>
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TelegramUserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="telegram-users-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'first_name') ?>

    <?= $form->field($model, 'last_name') ?>

    <?= $form->field($model, 'username') ?>

    <?= $form->field($model, 'chat_type') ?>

    <?php // echo $form->field($model, 'lang') ?>

    <?php // echo $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'is_bot') ?>

    <?php // echo $form->field($model, 'is_admin') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

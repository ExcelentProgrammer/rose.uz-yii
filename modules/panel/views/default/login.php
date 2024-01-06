<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Войти';
?>
<div class="outer">
    <div class="middle">
        <div class="inner">
            <h1 class="profile-name-card">Авторизация</h1>
            <?php $form = ActiveForm::begin([
                'id' => 'login-form',
            ]); ?>

            <?= $form->field($model, 'username')->textInput(['placeholder' => $model->getAttributeLabel('username')])->label(false) ?>

            <?= $form->field($model, 'password')->passwordInput(['placeholder' => $model->getAttributeLabel('password')])->label(false) ?>

            <?= $form->field($model, 'rememberMe')->checkbox(['class' => 'hidden'])->label(false) ?>

            <div class="form-group">
                <?= Html::submitButton('Войти', ['class' => 'btn btn-primary btn-block', 'name' => 'login-button']) ?>
            </div>
            <?php ActiveForm::end(); ?>
            <div class="copyright">
                &copy; <?= date("Y") ?> <a href="https://botagent.uz" target="_blank">Botagent </a>сделано с
                любовью
            </div>
        </div>
    </div>
</div>
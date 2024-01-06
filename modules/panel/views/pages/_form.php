<?php

use dosamigos\ckeditor\CKEditor;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Pages */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pages-form">

  <?php $form = ActiveForm::begin(); ?>

  <?= $form->errorSummary($model) ?>

  <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

  <?= $form->field($model, 'file')->fileInput() ?>

  <?= $form->field($model, 'title_ru')->textInput(['maxlength' => true]) ?>

  <?= $form->field($model, 'text_ru')->widget(CKEditor::className(), [
    'options' => ['rows' => 10],
    'preset' => 'advanced'
  ]) ?>

  <hr>

  <?= $form->field($model, 'title_uz')->textInput(['maxlength' => true]) ?>

  <?= $form->field($model, 'text_uz')->widget(CKEditor::className(), [
    'options' => ['rows' => 10],
    'preset' => 'advanced'
  ]) ?>

  <hr>

  <?= $form->field($model, 'title_uc')->textInput(['maxlength' => true]) ?>

  <?= $form->field($model, 'text_uc')->widget(CKEditor::className(), [
    'options' => ['rows' => 10],
    'preset' => 'advanced'
  ]) ?>

  <hr>

  <?= $form->field($model, 'title_en')->textInput(['maxlength' => true]) ?>

  <?= $form->field($model, 'text_en')->widget(CKEditor::className(), [
    'options' => ['rows' => 10],
    'preset' => 'advanced'
  ]) ?>


  <div class="form-group">
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success btn-fill']) ?>
  </div>

  <?php ActiveForm::end(); ?>

</div>

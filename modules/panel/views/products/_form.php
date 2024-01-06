<?php

use app\models\Category;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Products */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="products-form">

  <?php $form = ActiveForm::begin([
    'id' => 'create-form',
    'options' => [
      'enctype' => 'multipart/form-data',
    ]
  ]); ?>

  <?= $form->errorSummary($model) ?>

  <div class="form-group field-products-name required">
    <label class="control-label" for="products-name">Категории</label>
    <?= Html::dropDownList('categories', $model->getCategoriesId(), Category::getList(), ['multiple' => 'multiple', 'class' => 'form-control']) ?>
  </div>

  <hr>

  <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

  <?= $form->field($model, 'description')->textarea(['rows' => 4]) ?>

  <hr>

  <?= $form->field($model, 'name_uz')->textInput(['maxlength' => true]) ?>

  <?= $form->field($model, 'description_uz')->textarea(['rows' => 4]) ?>

  <hr>

  <?= $form->field($model, 'name_uc')->textInput(['maxlength' => true]) ?>

  <?= $form->field($model, 'description_uc')->textarea(['rows' => 4]) ?>

  <hr>

  <?= $form->field($model, 'name_en')->textInput(['maxlength' => true]) ?>

  <?= $form->field($model, 'description_en')->textarea(['rows' => 4]) ?>

  <hr>

  <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

  <?= $form->field($model, 'recommend')->checkbox() ?>

  <?= $form->field($model, 'hidden')->checkbox() ?>

  <?= $form->field($model, 'file')->fileInput() ?>

  <div class="form-group text-right">
    <?= Html::submitButton($model->isNewRecord ? 'Добавить' : 'Изменить', ['class' => $model->isNewRecord ? 'btn btn-success btn-fill' : 'btn btn-primary btn-fill']) ?>
  </div>

  <?php ActiveForm::end(); ?>

</div>

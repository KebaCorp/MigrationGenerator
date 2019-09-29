<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\GeneratorForm */
/* @var $form ActiveForm */

?>

<div class="site-generator-form">

    <?php $form = ActiveForm::begin(['action' => ['site/generate'], 'options' => ['method' => 'post']]); ?>

    <?= $form->field($model, 'directory') ?>
    <?= $form->field($model, 'framework')->dropDownList($model->frameworkList,
        ['value' => $model->framework]) ?>
    <?= $form->field($model, 'tables[]')->dropDownList($model->tablesList,
        ['multiple' => 'multiple', 'value' => $model->tables, 'size' => 20]) ?>
    <?= $form->field($model, 'dataTables[]')->dropDownList($model->dataTablesList,
        ['multiple' => 'multiple', 'value' => $model->dataTables, 'size' => 10]) ?>
    <?= $form->field($model, 'dbHost')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'dbPort')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'dbName')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'dbUser')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'dbPassword')->hiddenInput()->label(false) ?>
    <?= $form->field($model, 'dbCharset')->hiddenInput()->label(false) ?>

  <div class="form-group">
      <?= Html::submitButton(Yii::t('generator', 'Generate'), ['class' => 'btn btn-primary']) ?>
  </div>

    <?php ActiveForm::end(); ?>

</div>

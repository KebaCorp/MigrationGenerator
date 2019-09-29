<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ConnectionForm */
/* @var $form ActiveForm */

?>

<div class="site-connection-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'database')->dropDownList($model->databasesList, ['value' => $model->database]) ?>
    <?= $form->field($model, 'dbHost') ?>
    <?= $form->field($model, 'dbPort') ?>
    <?= $form->field($model, 'dbName') ?>
    <?= $form->field($model, 'dbUser') ?>
    <?= $form->field($model, 'dbPassword') ?>
    <?= $form->field($model, 'dbCharset') ?>

  <div class="form-group">
      <?= Html::submitButton(Yii::t('generator', 'Connect'), ['class' => 'btn btn-primary']) ?>
  </div>

    <?php ActiveForm::end(); ?>

</div>

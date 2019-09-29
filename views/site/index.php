<?php

use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $generatorForm app\models\GeneratorForm */
/* @var $connectionForm app\models\ConnectionForm */
/* @var $form ActiveForm */

?>

<div class="site-index">

    <?php

    if ($connectionForm) {
        echo $this->render('_connection-form', ['model' => $connectionForm]);
    }

    if ($generatorForm) {
        echo $this->render('_generator-form', ['model' => $generatorForm]);
    }

    ?>

</div>

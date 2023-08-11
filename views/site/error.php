<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = "Error"; //$name
?>
<div class="site-error">

    <h1><?php #Html::encode($this->title) ?></h1>

    <div class="alert alert-danger">
        <?= nl2br(Html::encode($message)) ?>
    </div>

    <p>
        Un error interno ha ocurrido al procesar la petición
    </p>
    <p>
        Contacta al administrador.
    </p>

</div>

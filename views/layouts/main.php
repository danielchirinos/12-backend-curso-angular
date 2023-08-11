<?php

use yii\helpers\Html;
use app\assets\AppAsset;
use app\assets\View;

$pagina = $this->params['activeLink'];
$bread1 = $this->params['bread1'];
$bread2 = $this->params['bread2'];
$bread3 = $this->params['bread3'];
$imagen = "";
$session = Yii::$app->session;
$idUsuario = $session['IdUsuario']; 
$idTipoUsuario = $session['tipo_usuario_id']; 
$nombreUsuario = $session['nombre']; 
$version = "2.1.6";

AppAsset::register($this);

?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
   
</head>
<body class="fixed-left-void widescreen">
<?php $this->beginBody() ?>

<?= $content ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

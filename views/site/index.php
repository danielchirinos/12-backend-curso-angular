<?php

use yii\widgets\ActiveForm;


    $this->title = 'Api - TMS';
    $this->params['bread1']  = $this->title;
    $this->params['bread2']  = '';
    $this->params['bread3']  = '';
    $this->params['activeLink'] = "apitms";
?>


<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">

    <title><?= $this->title ?></title>

    <style>
        body,html{
            height:100% !important;
        }

    </style>
  </head>
  <body>



    
      <?php if (Yii::$app->session->hasFlash('success')) { ?>
          
          <div class="alert alert-success" role="alert">
              <?= Yii::$app->session->getFlash('success'); ?>
          </div>
      <?php } ?>
      <?php if (Yii::$app->session->hasFlash('warning')) { ?>
          <div class="alert alert-warning" role="alert">
              <?= Yii::$app->session->getFlash('warning'); ?>
          </div>
      <?php } ?>
      <?php if (Yii::$app->session->hasFlash('error')) { ?>
          <div class="alert alert-danger" role="alert">
              <?= Yii::$app->session->getFlash('danger'); ?>
          </div>
      <?php } ?>

      <div class="container h-100">




          <div class="row h-100 justify-content-center align-items-center">
        <?php $form = ActiveForm::begin([
            'method' => 'post', 
            'id'=> 'formLogin', 
            'options'=> [
                "class" => "col-3"
            ],
                ]); ?>

                <div class="form-group">
                    <?= $form->field($model, 'usuario')->textInput(['class'=>'form-control col-md-12', 'placeholder' => 'Usuario'])->error(['class'=>'help-block badge badge-danger']) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'clave')->textInput(['class'=>'form-control col-md-12', 'type' => 'password', 'placeholder' => 'Clave'])->error(['class'=>'help-block badge badge-danger']) ?>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-success w-100" value="Iniciar sesion">
                </div>
  
                <?php $form->end(); ?>
            </div>
    </div>






    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
  </body>
</html>




<a href="/apidoc">Ir a documentación</a>
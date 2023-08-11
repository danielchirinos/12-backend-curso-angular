<?php
namespace app\controllers;

use Yii;
use stdClass;
use yii\web\Response;
use yii\web\Controller;
use app\models\Usuarios;
use app\models\_LoginForm;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\filters\AccessControl;
use app\models\ConfiguracionGlobal;

class SiteController extends Controller{

    public function actions(){
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
    

    public function actionIndex(){

        $model = new _LoginForm();
        if ($_POST) {

            $usuario = $_POST["_LoginForm"]["usuario"];
            $clave = $_POST["_LoginForm"]["clave"];
            $usuario = Usuarios::find()->where(["usuario" => $usuario, "clave" => $clave, "fecha_borrado" => null])->andWhere(["is not", "key_api", null])->one();

            if ($usuario != null) {
                $session = Yii::$app->session;
                $session->open();
                $session['IdUsuarioApi'] = $usuario->id;
                $session['nombreApi'] = $usuario->nombre;
                $session['apellidoApi'] = $usuario->apellido;
                $session['emailApi'] = $usuario->email;
                $session['usuarioApi'] = $usuario->usuario;					
                $session['claveApi'] = $usuario->clave;
                $session['keyApi'] = $usuario->key_api;

                return $this->redirect('/apidoc');
            }
            else{
                //return "no se pudo ingresar";
                Yii::$app->session->setFlash("warning","Usuario o clave incorrecta");
        
                return $this->render('index', ['model' => $model ]);
            }
        }
        return $this->render('index', ["model" => $model]);
    }



}

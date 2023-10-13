<?php

namespace app\controllers;

use Yii;
use stdClass;
use app\models\User;
use Firebase\JWT\JWT;
use yii\web\Response;
use yii\web\Controller;

class UsuarioController extends Controller{

    private $key = "danielprivatekey.cursoangular2023";

    public function actions(){
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    // function __contruct(){
    //     parent::__construct();
    //     
    // }

    public function beforeAction($action){     
        $this->enableCsrfValidation = false;
        Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    private function sendRequest($code, $estado, $message, $errors, $data){
        $respuesta = new stdClass();
        Yii::$app->response->statusCode = $code;
        $respuesta->code = $code;
        $respuesta->estado = $estado;
        $respuesta->message = $message;
        $respuesta->errors = $errors;
        $respuesta->data = $data;
        return $respuesta;
    }

    public function actionCrear(){

        try {
            $this->cabecerasPOST();

            date_default_timezone_set("America/Santiago");

            if (isset(getallheaders()["Authorization"])) {
                $token = getallheaders()["Authorization"];
                $decodeToken = $this->decodeToken($token);
                if($decodeToken->estado != "ok"){
                    return $this->sendRequest(401, "error", "Token Vencido", ["token vencido"], []);
                }
            }else{
                return $this->sendRequest(401, "error", "Token Invalido", ["token invalido"], []);
            }

            $respuesta = new stdClass();
            // if ($key != null) {

            if ($_POST) {
                $error = "Servicio Innacceible";
                return $this->sendRequest(405, "error", $error, [$error], []);
    
            }else{
                $post = file_get_contents('php://input');
                $data = json_decode($post);
    
                $_roleId = isset($data->role_id) ? $data->role_id : 1;
                $_email = isset($data->email) ? $data->email : null;
                $_name = isset($data->name) ? $data->name : null;
                $_password = isset($data->password) ? $data->password : null;
                $_isActive = isset($data->is_active) ? $data->is_active : null;
            
            }

            $errores = [];

            if (!isset($_roleId) || $_roleId =="" || $_roleId == null) {
                $errores[] = 'El rol es requerido';
            }
            if (!isset($_email) || $_email =="" || $_email == null) {
                $errores[] = 'El email es requerido';
            }
            if (!isset($_name) || $_name == "" || $_name == null) {
                $errores[] = 'El nombre es requerido';
            }
            if (!isset($_password) || $_password == "" || $_password == null) {
                $errores[] = 'El password es requerido';
            }
            if (!isset($_isActive) || $_isActive == "" || $_isActive == null) {
                $errores[] = 'El campo es activo es requerido';
            }
    
            if (count($errores) > 0) {
                return $this->sendRequest(400, "error", "Campos Requeridos",  $errores, []);
            }  

            $user = new User();
            $user->role_id = $_roleId;
            $user->email = $_email;
            $user->name = $_name;

            $optionsBcrypt = ['cost' => 10]; 
            $user->password = password_hash($_password, PASSWORD_BCRYPT, $optionsBcrypt);
            $user->is_active = $_isActive;

            if($user->save()){
                return $this->sendRequest(200, "ok", "Usuario creado con éxito", [], $user->id);
            }else{
                $erroresModel = [];
                foreach ($user->getErrors() as $key => $value) {
                    $erroresModel[] = $value[0];
                    break;
                }
                $error = "Ocurrio un error al crear el usuario";
                return $this->sendRequest(400, "error", $error, $erroresModel, []);
            }
        }catch (\Throwable $th) {
            $error = $th->getMessage();
            return $this->sendRequest(500, "error", "Ha ocurrido un error en el servidor al procesar la solicitud", [$error], []);
        }
    }

    //login desde app movil
        public function actionLogin(){

            try {
                $this->cabecerasPOST();

                date_default_timezone_set("America/Santiago");
                // $key = $this->validarKey(getallheaders()["Autorizacion"]);

                $respuesta = new stdClass();
                // if ($key != null) {

                    if ($_POST) {
                        $error = "Servicio Innacceible";
                        return $this->sendRequest(405, "error", $error, [$error], []);
            
                    }else{
                        $post = file_get_contents('php://input');
                        $data = json_decode($post);
            
                        $_email = isset($data->email) ? $data->email : null;
                        $_password = isset($data->password) ? $data->password : null;
                    }

                    $errores = [];

                    if (!isset($_email) || $_email =="" || $_email == null) {
                        $errores[] = 'El email es requerido';
                    }
                    if (!isset($_password) || $_password =="" || $_password == null) {
                        $errores[] = 'El password es requerido';
                    }
            
                    if (count($errores) > 0) {
                        return $this->sendRequest(400, "error", "Campos Requeridos",  $errores, []);
                    }
                    
                    $user = User::find()->where(['email' => $_email])->one();

                    if(!$user){
                        $error = "No existen usuarios con los datos ingresados";
                        return $this->sendRequest(401, "error", $error, [$error], []);
                    }
                    if($user->is_active == 0){
                        $error = "Usuario Inactivo";
                        return $this->sendRequest(400, "error", $error,  [$error], []);
                    }

                    if (!password_verify($_password, $user->password)) {
                        $error = "Clave invalida";
                        return $this->sendRequest(401, "error", $error,  [$error], []);
                    }


                    $token = $this->getToken(60); //crea un token con 1 hora de vigencia
                    $tokenRefresh = $this->getToken(300); //crea un token con 5 hora de vigencia

                    $res["user"] = [
                        "user_id" => $user->id,
                        "name" => $user->name,
                        "email" => $user->email,
                        "token" => $token["token"],
                        "token_refresh" => $tokenRefresh["token"],
                    ];


                    return $this->sendRequest(200, "ok", "Login exitoso", [], $res);

            } catch (\Throwable $th) {
                $error = $th->getMessage();
                return $this->sendRequest(500, "error", "Ha ocurrido un error en el servidor al procesar la solicitud", [$error], []);
            }
        }

        public function actionRefreshtoken(){
            try {
                $this->cabecerasGET();

                date_default_timezone_set("America/Santiago");

                if (isset(getallheaders()["Authorization"])) {
                    $token = getallheaders()["Authorization"];
                    $decodeToken = $this->decodeToken($token);
                    if($decodeToken->estado != "ok"){
                        return $this->sendRequest(401, "error", "Token Vencido", ["token vencido"], []);
                    }
                }else{
                    return $this->sendRequest(401, "error", "Token Invalido", ["token invalido"], $data);
                }

                $respuesta = new stdClass();
                $token = $this->getToken(1); //crea un token con 1 hora de vigencia
                $tokenRefresh = $this->getToken(5); //crea un token con 5 hora de vigencia

                if ($token) {
                    $data = [
                        "token" => $token["token"],
                        "token_refresh" => $tokenRefresh["token"],
                    ];

                    return $this->sendRequest(200, "ok", "Datos entregados", [], $data);
                    
                }else{
                    $error = "Ocurrio un error al actualizar los token";
                    return $this->sendRequest(400, "error", $error, [$error], []);
                }
            } catch (\Throwable $th) {
                $error = $th->getMessage();
                return $this->sendRequest(500, "error", "Ha ocurrido un error en el servidor al procesar la solicitud", [$error], []);
            }
        }



    //fin login desde app movil




    // funciones complementarias

        public function reemplazarAcentos($cadena){
            $cadena = str_replace("á", "A");
            $cadena = str_replace("é", "E");
            $cadena = str_replace("í", "I");
            $cadena = str_replace("ó", "O");
            $cadena = str_replace("ú", "U");
            $cadena = str_replace("Á", "A");
            $cadena = str_replace("É", "E");
            $cadena = str_replace("Í", "I");
            $cadena = str_replace("Ó", "O");
            $cadena = str_replace("Ú", "U");

            return $cadena;

        }

        public function getToken($tiempoMinutos){

            $tiempoCreacion = time(); //tiempo en que se creo el JWT
            $exp = $tiempoCreacion + 60 * $tiempoMinutos; //expiracion del token
            $payload = [
                "iss" => "localhost",
                "aud" => "localhost",
                "iat" => $tiempoCreacion,
                "exp" => $exp
            ];

            $jwt = JWT::encode($payload, $this->key, "HS512");

            return [
                "token" => $jwt,
                "exp" =>  $exp
            ];

        }

        public function decodeToken($token){

            $respuesta = new stdClass();
            try {
                $bearerToken = str_replace("Bearer ", "", $token);
                $tokenDecode = JWT::decode($bearerToken, $this->key, ["HS512"]);

                if(!isset($tokenDecode->exp)){
                    $respuesta->estado = "error";
                    $respuesta->respuesta = "token invalido";
                    $respuesta->mensaje = [];
                }else{
                    if(time() > $tokenDecode->exp){
                        $respuesta->estado = "error";
                        $respuesta->respuesta = "token vencido";
                        $respuesta->mensaje = [];
                    }else{
                        $respuesta->estado = "ok";
                        $respuesta->respuesta = "token vigente";
                        $respuesta->mensaje = [];
                    }
                }

    
                return $respuesta;
                
            } catch (\Throwable $th) {

                $respuesta->estado = "error";
                $respuesta->respuesta = "token expirado";
                $respuesta->mensaje = [];
                return $respuesta;
            }

        }

        public function actionEnviaremail(){
        
            if($_POST){
    
                $titulo = $_POST["titulo"];
                $correos = explode(",", $_POST["correos"]);
                $asunto = $_POST["asunto"];
    
                $email = Yii::$app->mailer
                ->compose("index", ["titulo" => $titulo])
                ->setTo($correos)
                ->setSubject($asunto)
                // ->setFrom() "nombre de donde se envie el correo"
                ->send();

                return $email;
            }
      
        }

        private function cabecerasPOST(){
            header('Access-Control-Allow-Origin: *');
            header("Access-Control-Allow-Methods: HEAD, GET, POST, PUT, PATCH, DELETE, OPTIONS");
            header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization, Autorizacion");
            header('Content-Type: application/json');
            $method = $_SERVER['REQUEST_METHOD'];
            if ($method == "OPTIONS") {
                header('Access-Control-Allow-Origin: *');
                header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization, Autorizacion");
                header("HTTP/1.1 200 OK");
                die();
            }
        }

        private function cabecerasGET(){
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, OPTIONS');
            header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization, Autorizacion");
            header('Content-Type: application/json');
            $method = $_SERVER['REQUEST_METHOD'];
            if ($method == "OPTIONS") {
                header('Access-Control-Allow-Origin: *');
                header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Access-Control-Request-Headers, Authorization, Autorizacion");
                header("HTTP/1.1 200 OK");
                die();
            }
        }

        
        
    // fin  funciones complementarias




}
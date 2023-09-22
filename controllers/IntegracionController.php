<?php

namespace app\controllers;

use Yii;
use stdClass;
use app\models\Zonas;
use Firebase\JWT\JWT;
use yii\web\Response;
use app\models\MZonas;
use app\models\Viajes;
use yii\web\Controller;
use app\models\CajaVale;
use app\models\Clientes;
use app\models\Usuarios;
use app\models\ViajePod;
use app\models\Auditoria;
use app\models\Documento;
use app\models\Rendicion;
use app\models\RutaGasto;
use app\models\TipoCarga;
use app\models\UsoChasis;
use app\models\Vehiculos;
use app\models\ViajesLog;
use app\models\EstatusPod;
use app\models\Macrozonas;
use app\models\Prefactura;
use app\models\TipoRampla;
use app\models\TipoViajes;
use app\models\ZonaAccion;
use app\models\Conductores;
use app\models\TipoServicio;
use app\models\ViajeDetalle;
use app\models\PrefacturaLog;
use app\models\TipoOperacion;
use app\models\UnidadNegocio;
use app\models\TipoDocumentos;
use app\models\Transportistas;
use app\models\ViajeDocumento;
use app\models\ViajeNovedades;
use app\models\RendicionMotivo;
use app\models\SubestatusViaje;
use app\models\ViajeDatosCarga;
use app\models\ViajeDetallePod;
use app\models\ViajePodDetalle;
use app\models\RendicionDetalle;
use app\models\ClienteFacturador;
use app\models\ContratoDocumento;
use app\models\PrefacturaDetalle;
use app\models\ClienteDirecciones;
use app\models\RendicionCategoria;
use app\models\ViajeDetalleAccion;
use app\models\ViajeDetalleRampla;
use app\models\ConfiguracionGlobal;
use app\models\ViajeDetallePodDetalle;
use app\models\ViajesCamposOpcionales;
use app\models\PrefacturaServicioAdicional;
use app\models\PrefacturaDocumentosReferencia;

class IntegracionController extends Controller{

    private $key = "bermannprivatekey.combersa363";

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

    //login desde app movil
        public function actionLogin(){

            try {
                $this->cabecerasPOST();

                date_default_timezone_set("America/Santiago");
                $key = $this->validarKey(getallheaders()["Autorizacion"]);

                $respuesta = new stdClass();
                if ($key != null) {

                    if ($_POST) {
                        $error = "Servicio Innacceible";
                        return $this->sendRequest(405, "error", $error, [$error], []);
            
                    }else{
                        $post = file_get_contents('php://input');
                        $data = json_decode($post);
            
                        $_usuario = isset($data->usuario) ? $data->usuario : null;
                        $_clave = isset($data->clave) ? $data->clave : null;
                        $_subdominio = isset($data->subdominio) ? $data->subdominio : null;
                    
                    }

                    $errores = [];

                    if (!isset($_usuario) || $_usuario =="" || $_usuario == null) {
                        $errores[] = 'El usuario es requerido';
                    }
                    if (!isset($_clave) || $_clave =="" || $_clave == null) {
                        $errores[] = 'El clave es requerido';
                    }
                    if (!isset($_subdominio) || $_subdominio == "" || $_subdominio == null) {
                        $errores[] = 'El subdominio es requerido';
                    }
            
                    if (count($errores) > 0) {
                        return $this->sendRequest(400, "error", "Campos Requeridos",  $errores, []);
                    }  

                    $asignarBD = Yii::$app->bermann->asignarBD($_subdominio);
                    if(!$asignarBD->asignada){
                        $error = "Subdominio invalido";
                        return $this->sendRequest(400, "error", $error, [$error], []);
                    }

                    
                    $model = Conductores::find()->where(['UPPER(usuario)' => mb_strtoupper($_usuario, "UTF-8")])->orWhere(["telefono" => mb_strtoupper($_usuario, "UTF-8")])->andWhere(['UPPER(clave)'=> mb_strtoupper($_clave, "UTF-8")])->one();

                    if ($model != NULL) {
                        if ($model->estado_conductor == 0) {
                            $error = "Conductor inactivo para aplicación movil";
                            return $this->sendRequest(400, "error", $error, [$error], []);
                        }else{

                            $token = $this->getToken(60); //crea un token con 1 hora de vigencia
                            $tokenRefresh = $this->getToken(300); //crea un token con 5 hora de vigencia

                            if ($token) {
                                if($model->foto == null){
                                    $imagen = '/images/icon_user.svg';
                                }else{
                                    $imagen = "{$asignarBD->urlRecursosExternos}/images/conductores/{$model->foto}";     
                                }

                                $data = [
                                    "id_conductor" => $model->id,
                                    "nombre_conductor" => $model->nombre,
                                    "imagen" => $imagen,
                                    "email" => $model->email == null ? "" : $model->email,
                                    "telefono" => $model->telefono == null ? "" : $model->telefono,
                                    "token" => $token["token"],
                                    // "token_exp" => $token["exp"],
                                    "token_refresh" => $tokenRefresh["token"],
                                    // "token_refresh_exp" => $tokenRefresh["exp"],
                                ];

                                return $this->sendRequest(200, "OK", "Datos entregados", [], $data);
                                
                            }else{
                                $error = "Ocurrio un error al validar el token";
                                return $this->sendRequest(400, "error", $error, [$error], []);
                            }
                        }
                    }else{
                        $error = "No existen conductores con los datos ingresados";
                        return $this->sendRequest(400, "error", $error, [$error], []);
                    }


                }else{
                    $error = "API_KEY invalida";
                    return $this->sendRequest(400, "error", $error, [$error], []);
                }
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

                    return $this->sendRequest(200, "OK", "Datos entregados", [], $data);
                    
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

    //valida patente y subdominio ingresados manualmente o por QR
    public function actionValidarsubdominio(){

        try {
            $this->cabecerasPOST();

            date_default_timezone_set("America/Santiago");
            $key = $this->validarKey(getallheaders()["Autorizacion"]);

            $respuesta = new stdClass();
            if ($key != null) {

                if ($_POST) {
                    $error = "Servicio Innacceible";
                    return $this->sendRequest(405, "error", $error, [$error], []);
                }else{
                    $post = file_get_contents('php://input');
                    $data = json_decode($post);

                    $_subdominio = isset($data->subdominio) ? $data->subdominio : null;
                
                }

                $errores = [];

                if (!isset($_subdominio) || $_subdominio =="" || $_subdominio == null) {
                    $errores[] = 'El clave es requerido';
                }
        
                if (count($errores) > 0) {
                    return $this->sendRequest(400, "error", "Campos requeridos", $errores, []);
                }  

                $asignarBD = Yii::$app->bermann->asignarBD($_subdominio);
                if(!$asignarBD->asignada){
                    $error = "Subdominio invalido";
                    return $this->sendRequest(400, "error", $error, [$error], []);
                }

                return $this->sendRequest(200, "ok", "Subdominio válidos", [], []);

            }else{
                return $this->sendRequest(400, "ok", "API_KEY invalida", ["API_KEY invalida"], $data);
            }
        } catch (\Throwable $th) {
            $error = $th->getMessage();
            return $this->sendRequest(500, "error", "Ha ocurrido un error en el servidor al procesar la solicitud", [$error], []);
        }
        
    }
    //fin valida patente y subdominio ingresados manualmente o por QR


    // /////////////////////////////////////////////////// VIAJES ////////////////////////////////////////////
        // listado de viajes por vehiculo
            public function actionListadoviajes(){

                try {
                    $this->cabecerasGET();
                    date_default_timezone_set('America/Santiago');
                    $respuesta = new stdClass();
                    
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
                    // if ($key != null) {

                    
                    if($_SERVER["REQUEST_METHOD"] == "GET"){
                        if ($_GET) {    
                            // $_patente = isset($data->patente) ? $data->patente : null;
                            $_conductorId = isset($_GET["conductor_id"]) ? $_GET["conductor_id"] : null;
                            $_accion = isset($_GET["accion"]) ? $_GET["accion"] : null;
                            $_fechaInicio = isset($_GET["fecha_inicio"]) ? $_GET["fecha_inicio"]. " 00:00:00" : null;
                            $_fechaFin = isset($_GET["fecha_fin"]) ? $_GET["fecha_fin"]. " 23:59:59" : null;
                            $_subdominio = isset($_GET["subdominio"]) ? $_GET["subdominio"] : null;
                        }else{
                            // $error = "Servicio Innacceible";
                            // return $this->sendRequest(405, "error", $error, [$error], []);
                            $post = file_get_contents('php://input');
                            $data = json_decode($post);
        
                            // $_patente = isset($data->patente) ? $data->patente : null;
                            $_conductorId = isset($data->conductor_id) ? $data->conductor_id : null;
                            $_accion = isset($data->accion) ? $data->accion : null;
                            $_fechaInicio = isset($data->fecha_inicio) ? $data->fecha_inicio. " 00:00:00" : null;
                            $_fechaFin = isset($data->fecha_fin) ? $data->fecha_fin. " 23:59:59" : null;
                            $_subdominio = isset($data->subdominio) ? $data->subdominio : null;
                        }
                    }else{
                        $error = "Servicio Innacceible";
                        return $this->sendRequest(405, "error", $error, [$error], []);
                    }            
    
                        
                    //validaciones de requeridos
                        $errores = [];
            
                        // if (!isset($_patente) || $_patente == "" || $_patente == null) {
                        //     $errores[] = 'El campo patente es requerido';
                        // }
            
                        if (!isset($_conductorId) || $_conductorId == "" || $_conductorId == null) {
                            $errores[] = 'El campo conductor_id es requerido';
                        }
                        if (!isset($_accion) || $_accion === "" || $_accion === null) {
                            $errores[] = 'El campo accion es requerido';
                        }
                        // si la accion es todos, deben venir las fechas
                            if($_accion == 1){
                                if (!isset($_fechaInicio) || $_fechaInicio == "" || $_fechaInicio == null) {
                                    $errores[] = 'El campo fecha_inicio es requerido';
                                }
                                if (!isset($_fechaFin) || $_fechaFin == "" || $_fechaFin == null) {
                                    $errores[] = 'El campo fecha_fin es requerido';
                                }
                            }
                        // fin si la accion es todos, deben venir las fechas
                        if (!isset($_subdominio) || $_subdominio == "" || $_subdominio == null) {
                            $errores[] = 'El campo subdominio es requerido';
                        }

                        if (count($errores) > 0) {
                            return $this->sendRequest(400, "error", "Campos Requeridos", $errores, []);
                        }
                    //fin validaciones de requeridos
    
                    $asignarBD = Yii::$app->bermann->asignarBD($_subdominio);
                    if(!$asignarBD->asignada){               
                        $error = "Subdominio invalido";
                        return $this->sendRequest(400, "error", $error, [$error], []);
                    }
    
                    // validar accion operacion para el case
                        $accionOperacion = $_accion;
                        if (intval($_accion) > 1) {
                            $accionOperacion = 0;
                        }       
                    // fin validar accion operacion para el case
    
                    switch ($accionOperacion) {
                        //viajes activos del dia actual
                        case 0:
                            $model = Viajes::find()->where(["conductor_id" => $_conductorId ])->andWhere(['BETWEEN', 'fecha_presentacion', date("Y-m-d 00:00:00"), date("Y-m-d 23:59:59")])->andWhere(["not in", "estatus_viaje_id", [1,6,9]])->orderBy(["id" => SORT_DESC])->all();   
                            break;
                        //todos los viajes
                        case 1:
                            $model = Viajes::find()->where(["conductor_id" => $_conductorId ])->andWhere(['BETWEEN', 'fecha_presentacion', $_fechaInicio, $_fechaFin])->andWhere(["not in", "estatus_viaje_id", [1,6,9]])->orderBy(["id" => SORT_DESC])->all();  
                            break; 
                    }
    
                    if ($model != NULL) {
                        $viajes = [];
    
                        foreach ($model as $k => $v) {
    
                            $transportista = $v->transportista_id != '' ? $v->transportista->nombre : '';
                            $vehiculoUno = $v->vehiculo_uno_id != '' ? $v->vehiculoUno->patente : '';
                            $muestraUno = $v->vehiculo_uno_id != '' ? $v->vehiculoUno->muestra : '';
                            $vehiculoDos = $v->vehiculo_dos_id != '' ? $v->vehiculoDos->patente : '';
                            $muestraDos = $v->vehiculo_dos_id != '' ? $v->vehiculoDos->muestra : '';
                            $conductor = $v->conductor_id != '' ? $v->conductor->nombre : '';
                            $carga = $v->carga != null ? $v->carga->codigo_carga : '';
    
                            $viaje = new stdClass();
                            $viaje->hoja_ruta = $v->hojaRuta->nro_hr;
                            $viaje->viaje_id = $v->id;
                            $viaje->carga = $v->carga->codigo_carga;
                            $viaje->nro_viaje = $v->nro_viaje == null ? 0 : $v->nro_viaje;
                            $viaje->fecha = date_format(date_create($v->fecha),'d/m/Y');
                            $viaje->cliente = $v->cliente_id != '' ? $v->cliente->nombre : '';
                            $viaje->transportista = $transportista;
                            $viaje->vehiculo_uno = $vehiculoUno;
                            $viaje->muestra_uno = $muestraUno;
                            $viaje->muestra_dos = $muestraDos;
                            $viaje->vehiculo_dos = $vehiculoDos;
                            $viaje->conductor = $conductor;
                            $viaje->nro_carga = $carga;
                            $viaje->fecha_servidor = date("Y-m-d H:i:s");

                            
                            $viajePod = ViajePod::find()->where(["viaje_id" => $v->id])->one();
                            
                            $contadorPod = 0;
                            $contadorNovedades = 0;
                            if ($viajePod) {

                                $viajePodDetalle = ViajePodDetalle::find()->where(["viaje_pod_id" => $viajePod->id])->all();
                                foreach ($viajePodDetalle as $kvpd => $vvpd) {
                                    $contadorPod++;
                                }
    
                            }
                            
                            $viajeDetalle = ViajeDetalle::find()->where(["viaje_id" => $v->id])->orderBy(["orden" => SORT_ASC])->all();

                            foreach ($viajeDetalle as $kvdn => $vvdn) {
                                $novedades = ViajeNovedades::find()->where(["viaje_detalle_id" => $vvdn->id])->all();

                                if ($novedades) {
                                    foreach ($novedades as $vn) {
                                        $contadorNovedades++;
                                    }  
                                }
                            }  

                            $viaje->contadorPOD = $contadorPod;
                            $viaje->contadorNovedades = $contadorNovedades;
    
                            $destinos = [];
    
                            $vDetalle = ViajeDetalle::find()->where(["viaje_id" => $v->id])->orderBy(["orden" => SORT_ASC])->all();
    
                            foreach ($vDetalle as $k => $parada) {
                                $detalle = new stdClass();
                                $detalle->id_parada = $parada->id;
                                $detalle->orden = $parada->orden;
                                $detalle->fecha_entrada = date_format(date_create($parada->fecha_entrada),'d/m/Y H:i');
                                $detalle->zona = $parada->zona->nombre;
                                $detalle->zona_id = $parada->zona_id;
    
                                $detalle->zona_direccion = "";
                                if($parada->zona){
                                    $detalle->zona_direccion = $parada->zona->direccion ?? "" ;
                                }
    
                                $detalle->categoria = $parada->zona->zonaCategoria->nombre;
    
                                $estado = "Sin Eventos a Tiempo"; //cuando es = a 0
                                switch ($parada->estado) {
                                    case 1:
                                        $estado = "Sin Eventos Atrasado";
                                        break;
                                    case 2:
                                        $estado = "Con Eventos a Tiempo";
                                        break;
                                    case 3:
                                        $estado = "Con Eventos atrasdao";
                                        break;
                                }
                                $detalle->estado_parada = $estado;
                                
                                $destinos[] = $detalle;
                            }
                            $viaje->destinos = $destinos;
                            $viajes[] = $viaje;
                        }
    
                        return $this->sendRequest(200, "ok", "Datos entregados", [], $viajes);
                        
                    }else{
    
                        switch ($accionOperacion) {
                            //viajes activos del dia actual
                            case 0:
                                $mensaje = "No existen viajes asociados al conductor, para hoy";
                                return $this->sendRequest(404, "ok", $mensaje, [$mensaje], []);
                            break;
                            //viajes activos del dia de mañana
                            case 1:
                                $mensaje = "No existen viajes asociados al conductor, para mañana";
                                return $this->sendRequest(404, "ok", $mensaje, [$mensaje], []);
                            break; 
                            //viajes completados dia actual
                            case 2:
                                $mensaje = "No existen viajes asociados al conductor, completados para hoy";
                                return $this->sendRequest(404, "ok", $mensaje, [$mensaje], []);
                            break;
                        }
                        
                    }
                } catch (\Throwable $th) {
                    $error = $th->getMessage();
                    return $this->sendRequest(500, "error", "Ha ocurrido un error en el servidor al procesar la solicitud", [$error], []);
                }
            }
        //fin  listado de viajes por vehiculo

        // listado de viajes historico
            public function actionListadoviajeshistorico(){
                date_default_timezone_set('America/Santiago');
            
                $key = $this->validarKey(getallheaders()["Autorizacion"]);

    
                $respuesta = new stdClass();
                if ($key != null) {
            
                    if ($_POST) {

                        $_patente = isset($_POST["patente"]) ? $_POST["patente"] : null ;
                        $_fechaDesde = isset($_POST["fecha_desde"]) ? $_POST["fecha_desde"] : null ;
                        $_fechaHasta = isset($_POST["fecha_hasta"]) ? $_POST["fecha_hasta"] : null ;
            
                    }else{
                        $post = file_get_contents('php://input');
                        $data = json_decode($post);
            
                        $_patente = isset($data->patente) ? $data->patente : null;
                        $_fechaDesde = isset($data->fecha_desde) ? $data->fecha_desde : null;
                        $_fechaHasta = isset($data->fecha_hasta) ? $data->fecha_hasta : null;
            
                    }
                    
                    //validaciones de requeridos
                        $errores = [];
            
                        if (!isset($_patente) || $_patente == "" || $_patente == null) {
                            $errores[] = 'El campo patente es requerido';
                        }
                        if (!isset($_fechaDesde) || $_fechaDesde == "" || $_fechaDesde == null) {
                            $errores[] = 'El campo fecha_desde es requerido';
                        }
                        if (!isset($_fechaHasta) || $_fechaHasta == "" || $_fechaHasta == null) {
                            $errores[] = 'El campo fecha_hasta es requerido';
                        }
            
                        if (count($errores) > 0) {
                            $respuesta->estado = "error";
                            $respuesta->respuesta = "detalle errores";
                            $respuesta->mensaje = $errores;
                            return $respuesta;
                        }
                    //fin validaciones de requeridos



                    // if ($_POST) {
                        // validar vehiculo
                            $patente = Vehiculos::find()->where(["patente" => $_patente])->one();

                            
                            if (!$patente) {
                                $respuesta->estado = "error";
                                $respuesta->respuesta = "No existe esta patente asociada a ningun vehiculo";
                                $respuesta->mensaje = [];
                                return $respuesta;
                            }else{
                                $patenteId = $patente->id;
                            }
                        
                        // fin validar vehiculo

                        $model = Viajes::find()->where(['vehiculo_uno_id' => $patenteId])->andWhere(['BETWEEN', 'fecha_presentacion', $_fechaDesde, $_fechaHasta])->all(); 

        
                        if ($model != NULL) {
                            $viajes = [];

                            foreach ($model as $k => $v) {
        
                                $transportista = $v->transportista_id != '' ? $v->transportista->nombre : '';
                                $vehiculoUno = $v->vehiculo_uno_id != '' ? $v->vehiculoUno->patente : '';
                                $vehiculoDos = $v->vehiculo_dos_id != '' ? $v->vehiculoDos->patente : '';
                                $conductor = $v->conductor_id != '' ? $v->conductor->nombre : '';
        
                                $viaje = new stdClass();
                                $viaje->hoja_ruta = $v->hojaRuta->nro_hr;
                                $viaje->viaje_id = $v->id;
                                $viaje->nro_viaje = $v->nro_viaje == null ? 0 : $v->nro_viaje;
                                $viaje->fecha = date_format(date_create($v->fecha),'d/m/Y');
                                $viaje->cliente = $v->cliente_id != '' ? $v->cliente->nombre : '';
                                $viaje->transportista = $transportista;
                                $viaje->vehiculo_uno = $vehiculoUno;
                                $viaje->vehiculo_dos = $vehiculoDos;
                                $viaje->conductor = $conductor;
                                $viaje->fecha_servidor = date("Y-m-d H:i:s");
        
                                
                                $viajeDetalle = ViajeDetalle::find()->where(["viaje_id" => $v->id])->all();
                                if ($viajeDetalle) {
                                    $contadorPod = 0;
                                    $contadorSinPod = 0;
                                    $contadorNovedades = 0;
                                    foreach ($viajeDetalle as $key => $value) {
        
                                        // echo $value->id;exit;
                                        $pod = ViajeDetallePod::find()->where(["viaje_detalle_id" => $value->id])->all();
        
                                        if ($pod) {
                                            foreach ($pod as $vp) {
                                                $contadorPod++;
                                            }  
                                        }else{
                                            $contadorSinPod++;
                                        }
        
                                        $novedades = ViajeNovedades::find()->where(["viaje_detalle_id" => $value->id])->all();
        
                                        if ($novedades) {
                                            foreach ($novedades as $vn) {
                                                $contadorNovedades++;
                                            }  
                                        }
                                    }
                                }
        
                                $viaje->contadorPOD = $contadorPod;
                                $viaje->contadorSinPOD = $contadorSinPod;
                                $viaje->contadorNovedades = $contadorNovedades;
        
                                $destinos = [];
        
                                foreach ($v->viajeDetalles as $k => $parada) {
                                    $detalle = new stdClass();
                                    $detalle->id = $parada->id;
                                    $detalle->orden = $parada->orden;
                                    $detalle->fecha_entrada = date_format(date_create($parada->fecha_entrada),'d/m/Y H:i');
                                    $detalle->zona = $parada->zona->nombre;
                                    $detalle->zona_id = $parada->zona_id;
                                    $detalle->zona_direccion = "";
                                    if($parada->zona){
                                        $detalle->zona_direccion = $parada->zona->direccion ?? "" ;
                                    }
                                    $detalle->categoria = $parada->zona->zonaCategoria->nombre;
        
                                    $estado = "Sin Eventos a Tiempo"; //cuando es = a 0
                                    switch ($parada->estado) {
                                        case 1:
                                            $estado = "Sin Eventos Atrasado";
                                            break;
                                        case 2:
                                            $estado = "Con Eventos a Tiempo";
                                            break;
                                        case 3:
                                            $estado = "Con Eventos atrasdao";
                                            break;
                                    }
                                    $detalle->estado_parada = $estado;
                                    
                                    $destinos[] = $detalle;
                                }
                                $viaje->destinos=$destinos;
                                $viajes[] = $viaje;
                            }

                            // $respuesta->estado = "ok";
                            // $respuesta->mensaje = json_encode();

                            $respuesta->estado = "ok";
                            $respuesta->respuesta = "datos entregados";
                            $respuesta->mensaje = $viajes;
                        }else{
                            $respuesta->estado = "ok";
                            $respuesta->respuesta = 'No existen viajes asociados a la patente';
                            $respuesta->mensaje = [];
                        }

                // }


                }else{

                    $respuesta->estado = "error";
                    $respuesta->respuesta = "API_KEY invalida";
                    $respuesta->mensaje = [];
                }
            
                return $respuesta;

            }
        // fin listado de viajes historico


        //datos numero de planilla
            public function actionNumeroplanilla(){

                $this->cabecerasPOST();
                date_default_timezone_set("America/Santiago");
            
                $respuesta = new stdClass();
                if (isset(getallheaders()["Authorization"])) {
                    $token = getallheaders()["Authorization"];
                    $decodeToken = $this->decodeToken($token);
                    if($decodeToken->estado != "ok"){
                        return $this->sendRequest(401, "error", "Token Vencido", ["token vencido"], []);
                    }

                }else{
                    $respuesta->estado = "error";
                    $respuesta->respuesta = "token invalido";
                    $respuesta->mensaje = [];
                    return $respuesta;
                }
                
                if ($_POST) {
                    $_viajeId = isset($_POST["viaje_id"]) ? $_POST["viaje_id"] : null;
                    $_numeroPlanilla = isset($_POST["numero_planilla"]) ? $_POST["numero_planilla"] : null;
                }else{
                    $post = file_get_contents('php://input');
                    $data = json_decode($post);
        
                    $_viajeId = isset($data->viaje_id) ? $data->viaje_id : null;
                    $_numeroPlanilla = isset($data->numero_planilla) ? $data->numero_planilla : null;
                }
        
                //validaciones de requeridos
                    $errores = [];

                    if (!isset($_viajeId) || $_viajeId == "" || $_viajeId == null) {
                        $errores[] = 'El campo viaje_id es requerido';
                    }
                    if (!isset($_numeroPlanilla) || $_numeroPlanilla == "" || $_numeroPlanilla == null) {
                        $errores[] = 'El campo numero_planilla es requerido';
                    }
        
                    if (count($errores) > 0) {
                        $respuesta->estado = "error";
                        $respuesta->respuesta =  "detalle errores";
                        $respuesta->mensaje = $errores;
                        return $respuesta;
                    }
                //fin validaciones de requeridos
                    
                $viaje = Viajes::findOne($_viajeId);

                if ($viaje) {

                    if($viaje->numero_planilla != null){
                        $respuesta->estado = "error";
                        $respuesta->respuesta = "Este viaje ya tiene un número de planilla asociado";
                        $respuesta->mensaje = [];
                    }else{
                        // se valida que el numero de planilla no exista para otro viaje del mismo cliente

                            $viajeCliente = Viajes::find()->where(["cliente_id" => $viaje->cliente_id, "numero_planilla" => $_numeroPlanilla])->one();

                            if($viajeCliente){
                                $respuesta->estado = "error";
                                $respuesta->respuesta =  "El número de planilla ya existe para el viaje ".$viajeCliente->id." - nro de viaje ". $viajeCliente->nro_viaje;
                                $respuesta->mensaje = $errores;
                                return $respuesta;
                            }else{
                                $viaje->numero_planilla = $_numeroPlanilla;
                                if ($viaje->update()) {
                                    $respuesta->estado = "ok";
                                    $respuesta->respuesta = "Número de planilla agregado";
                                    $respuesta->mensaje = [];
                                }else{
                                    $respuesta->estado = "error";
                                    $respuesta->respuesta = "Ocurrio un error al agregar el numero de planilla";
                                    $respuesta->mensaje = [];
                                }
                            }               
                        // fin se valida que el numero de planilla no exista para otro viaje del mismo cliente
                    }
                }else{
                    $respuesta->estado = "error";
                    $respuesta->respuesta = "El viaje no existe en TMS";
                    $respuesta->mensaje = [];
                }
            
                return $respuesta;
                
            }
        //fin datos de carga del viaje

        //viajes sin rendir  por conductor
            public function actionViajessinrendirconductor(){

                $this->cabecerasGET();
                date_default_timezone_set("America/Santiago");
            
                $respuesta = new stdClass();
                if (isset(getallheaders()["Authorization"])) {
                    $token = getallheaders()["Authorization"];
                    $decodeToken = $this->decodeToken($token);
                    if($decodeToken->estado != "ok"){
                        return $this->sendRequest(401, "error", "Token Vencido", ["token vencido"], []);
                    }

                }else{
                    $respuesta->estado = "error";
                    $respuesta->respuesta = "token invalido";
                    $respuesta->mensaje = [];
                    return $respuesta;
                }
            
                if ($_GET) {
                    $_conductorId = isset($_GET["conductor_id"]) ? $_GET["conductor_id"] : null;
                }else{
                    $respuesta->estado = "error";
                    $respuesta->respuesta = "servicio inaccesible";
                    $respuesta->mensaje = [];
                    return $respuesta;
                }
        
                //validaciones de requeridos
                    $errores = [];

                    if (!isset($_conductorId) || $_conductorId == "" || $_conductorId == null) {
                        $errores[] = 'El campo conductor_id es requerido';
                    }
        
                    if (count($errores) > 0) {
                        $respuesta->estado = "error";
                        $respuesta->respuesta =  "detalle errores";
                        $respuesta->mensaje = $errores;
                        return $respuesta;
                    }
                //fin validaciones de requeridos
                    
                // $viajes = Viajes::find()->where(["conductor_id" => $_conductorId])->andWhere(["in", "estatus_viaje_id" , [2,4,5,6]])->orderBy(["id" => SORT_ASC])->all();


                // se buscan todos los viajes con o sin rendiciones
                $db = Yii::$app->get("db");
                $viajes = $db->createCommand("SELECT v.*, hr.nro_hr, cl.nombre as cliente, cr.codigo_carga, ru.dias_ruta
                from viajes v
                left join rendicion r on v.id = r.viaje_id 
                inner join hoja_ruta hr on hr.id = v.hoja_ruta_id
                inner join carga cr on cr.id = v.carga_id
                inner join clientes cl on cl.id = v.cliente_id
                inner join ruta ru on ru.id = v.ruta_id
                inner join viajes_cierre_operativo vco on vco.viaje_id = v.id
                where (r.estado <> 1 or r.id is null) and v.conductor_id = {$_conductorId} and v.estatus_viaje_id = 6
                order by 1");

                $viajes = $viajes->queryAll(); 

                $suma = 0;
                if (count($viajes) > 0) {

                    $viajeArreglo = [];
                    foreach ($viajes as $k => $v) {
                        
                        $viaje = new stdClass();
                        $viaje->viaje_id = $v["id"];
                        $viaje->nro_viaje = $v["nro_viaje"] == null ? 0 : $v["nro_viaje"];
                        $viaje->nro_planilla = $v["numero_planilla"] ?? "sin número asignado";
                        $viaje->hoja_ruta = $v["nro_hr"];
                        $viaje->carga = $v["codigo_carga"];
                        $viaje->fecha = date_format(date_create($v["fecha"]),'d/m/Y');
                        $viaje->cliente = $v["cliente"];
                        $viaje->aprovisionamiento = number_format($v["monto_aprovisionado"] ?? 0, 2, ',', '.');
                        
                        $viajeDetalle = ViajeDetalle::find()->where(["viaje_id" => $v["id"]])->orderBy(["orden" => SORT_ASC])->all();
                        $viaje->origen = $viajeDetalle[0]->zona->nombre;
                        $viaje->destino = $viajeDetalle[count($viajeDetalle)- 1]->zona->nombre;
                        $fechaDestino = $viajeDetalle[count($viajeDetalle) - 1]->fecha_entrada;
                        if($fechaDestino){
                            $viaje->fecha_presentacion = date_format(date_create($fechaDestino),'d/m/Y H:i');
                        }

                        // se agregan los gastos
                        $gastosArreglo = [];
                        
                        // se buscan todos los gastos que tenga la ruta
                        $rutaGastos = $db->createCommand("SELECT rg.*, g.nombre 
                        from ruta_gasto rg 
                        inner join gasto g on g.id = rg.gasto_id
                        where rg.ruta_id = {$v['ruta_id']} and rg.fecha_borrado is null");
            
                        $rutaGastos = $rutaGastos->queryAll(); 
                        
                        if(count($rutaGastos) > 0){
                            foreach ($rutaGastos as $krg => $vrg) {
                                $gastos = new stdClass();
                                $gastos->nombre = $vrg["nombre"];
                                $gastos->monto = number_format($vrg["monto"], 2, ',', '.');
                                $gastosArreglo[] = $gastos;
                            }
                        }

                        // fin se agregan los gastos

                        // se agrega el viatico
                            if($v["dias_ruta"] != null){
                                $viatico = new stdClass();
                                $viatico->nombre = "Viatico";
                                $viatico->monto = number_format($v["dias_ruta"] * 12000, 2, ',', '.');
                                $gastosArreglo[] = $viatico;
                            }
                        //sin de agrega el viatico 


                        $viaje->gastos = $gastosArreglo;


                        // se agregan las rendiciones si tiene
                            

                            // se busca la rendicion del viaje
                            $rendicion = $db->createCommand("SELECT * from rendicion r where r.viaje_id = {$v['id']}");
                            $rendicion = $rendicion->queryAll(); 

                            // si exite una rendicion
                            if(count($rendicion) == 1){
                                // $rendicionDetalle = RendicionDetalle::find()->where(["rendicion_id" => ])->orderBy(["fecha_creacion" => SORT_ASC])->all();

                                $rendicionDetalle = $db->createCommand("SELECT rd.*, rm.nombre as motivo
                                from rendicion_detalle rd
                                inner join rendicion_motivo rm on rm.id = rd.motivo_rendicion_id
                                where rd.rendicion_id = {$rendicion[0]['id']}");
                                
                                $rendicionDetalle = $rendicionDetalle->queryAll(); 

                                $rendicionArreglo = [];
                                if(count($rendicionDetalle) > 0){

                                    foreach ($rendicionDetalle as $krd => $vrd) {
                                        $r = new stdClass();
                                        $r->fecha_rendicion = \Datetime::createFromFormat("Y-m-d H:i:s", $vrd["fecha_boleta"])->format("d/m/Y H:i:s");
                                        $r->monto = number_format($vrd["monto"], 2, ',', '.');
                                        $r->motivo = $vrd["motivo"]; 
                                        $r->estado = $vrd["validado"] == 0 ? "Sin validar" : "Validado"; 
                                        $rendicionArreglo[] = $r;
                                    }
                                }

                                $viaje->rendiciones = $rendicionArreglo;

                                // si no ha rendido nada, se agrega el monto de rendicion total al valor por rendir
                                $montoRendicion = (floatval($rendicion[0]["monto_saldo"] == 0)) ?  ($v["monto_aprovisionado"] ?? 0) : $rendicion[0]["monto_saldo"];
                                if(count($rendicionArreglo) > 0){
                                    $viaje->monto_por_rendir = abs($rendicion[0]["monto_saldo"]);
                                }else{
                                    $viaje->monto_por_rendir = abs($montoRendicion);
                                }

                                $suma = $suma + floatval($viaje->monto_por_rendir);

                                $viaje->monto_por_rendir =  number_format($viaje->monto_por_rendir, 2, ',', '.'); 

                            }else{

                                // // si no tiene rendicion se agrega el monto aprovisionado del viaje
                                // $r = new stdClass();
                                // $r->fecha_rendicion = "sin fecha";
                                // $r->monto = number_format($v["monto_aprovisionado"], 2, ',', '.');
                                // $r->motivo = "sin motivo"; 
                                // $r->estado = "sin validar"; 
                                // $rendicionArreglo[] = $r;
                                
                                // $viaje->rendiciones = $rendicionArreglo;
                                $viaje->rendiciones = [];

                                $viaje->monto_por_rendir = number_format(floatval(abs($v["monto_aprovisionado"]) ?? 0), 2, ',', '.');

                                $suma = $suma + floatval($v["monto_aprovisionado"]);

                            }

                        // fin se agregan las rendiciones si tiene


                        $viajeArreglo[] = $viaje;

                    }

                    $respuesta->estado = "ok";
                    $respuesta->respuesta = "datos entregados";
                    $respuesta->totalPorRendir = number_format(($suma ?? 0), 2, ',', '.');
                    $respuesta->mensaje = $viajeArreglo;

                }else{
                    $respuesta->estado = "error";
                    $respuesta->respuesta = "No existen viajes pendientes por rendir";
                    $respuesta->totalPorRendir = $suma;
                    $respuesta->mensaje = [];
                }
            
                return $respuesta;
                
            }
        //fin viajes sin rendir  por conductor


        //viajes con rendicion cerrada por conductor
            public function actionViajesrendidosconductor(){
                date_default_timezone_set("America/Santiago");
            
                $this->cabecerasGET();
                
                $respuesta = new stdClass();
                if (isset(getallheaders()["Authorization"])) {
                    $token = getallheaders()["Authorization"];
                    $decodeToken = $this->decodeToken($token);
                    if($decodeToken->estado != "ok"){
                        return $this->sendRequest(401, "error", "Token Vencido", ["token vencido"], []);
                    }

                }else{
                    $respuesta->estado = "error";
                    $respuesta->respuesta = "token invalido";
                    $respuesta->mensaje = [];
                    return $respuesta;
                }
            
                if ($_GET) {
                    $_conductorId = isset($_GET["conductor_id"]) ? $_GET["conductor_id"] : null;
                }else{
                    $respuesta->estado = "error";
                    $respuesta->respuesta = "servicio inaccesible";
                    $respuesta->mensaje = [];
                    return $respuesta;
                }
        
                //validaciones de requeridos
                    $errores = [];

                    if (!isset($_conductorId) || $_conductorId == "" || $_conductorId == null) {
                        $errores[] = 'El campo conductor_id es requerido';
                    }
        
                    if (count($errores) > 0) {
                        $respuesta->estado = "error";
                        $respuesta->respuesta =  "detalle errores";
                        $respuesta->mensaje = $errores;
                        return $respuesta;
                    }
                //fin validaciones de requeridos
                    
                // $viajes = Viajes::find()->where(["conductor_id" => $_conductorId])->andWhere(["in", "estatus_viaje_id" , [2,4,5,6]])->orderBy(["id" => SORT_ASC])->all();

                $db = Yii::$app->get("db");
                $viajes = $db->createCommand("SELECT v.*, hr.nro_hr, cl.nombre as cliente, cr.codigo_carga, ru.dias_ruta, r.fecha_creacion as fecha_rendicion
                from viajes v
                left join rendicion r on v.id = r.viaje_id 
                inner join hoja_ruta hr on hr.id = v.hoja_ruta_id
                inner join carga cr on cr.id = v.carga_id
                inner join clientes cl on cl.id = v.cliente_id
                inner join ruta ru on ru.id = v.ruta_id
                where (r.estado = 1) and v.conductor_id = {$_conductorId}
                order by 1");

                $viajes = $viajes->queryAll(); 

                if (count($viajes) > 0) {

                    $viajeArreglo = [];

                    foreach ($viajes as $k => $v) {
                        
                        $viaje = new stdClass();
                        $viaje->viaje_id = $v["id"];
                        $viaje->nro_viaje = $v["nro_viaje"] == null ? 0 : $v["nro_viaje"];
                        $viaje->nro_planilla = $v["numero_planilla"];
                        $viaje->hoja_ruta = $v["nro_hr"];
                        $viaje->carga = $v["codigo_carga"];
                        $viaje->fecha = date_format(date_create($v["fecha"]),'d/m/Y');
                        $viaje->fecha_rendicion = date_format(date_create($v["fecha_rendicion"]),'d/m/Y');
                        $viaje->cliente = $v["cliente"];
                        $viaje->aprovisionamiento = number_format($v["monto_aprovisionado"] ?? 0, 2, ',', '.');
                        
                        $viajeDetalle = ViajeDetalle::find()->where(["viaje_id" => $v["id"]])->orderBy(["orden" => SORT_ASC])->all();
                        $viaje->origen = $viajeDetalle[0]->zona->nombre;
                        $viaje->destino = $viajeDetalle[count($viajeDetalle)- 1]->zona->nombre;
                        $fechaDestino = $viajeDetalle[count($viajeDetalle) - 1]->fecha_entrada;
                        if($fechaDestino){
                            $viaje->fecha_presentacion = date_format(date_create($fechaDestino),'d/m/Y H:i');
                        }

                        // se agregan los gastos
                        $gastosArreglo = [];
                        
                        $rutaGastos = $db->createCommand("SELECT rg.*, g.nombre 
                        from ruta_gasto rg 
                        inner join gasto g on g.id = rg.gasto_id
                        where rg.ruta_id = {$v['ruta_id']} and rg.fecha_borrado is null");
            
                        $rutaGastos = $rutaGastos->queryAll(); 
                        
                        if(count($rutaGastos) > 0){
                            foreach ($rutaGastos as $krg => $vrg) {
                                $gastos = new stdClass();
                                $gastos->nombre = $vrg["nombre"];
                                $gastos->monto = number_format($vrg["monto"], 2, ',', '.');
                                $gastosArreglo[] = $gastos;
                            }
                        }

                        // fin se agregan los gastos

                        // se agrega el viatico
                            if($v["dias_ruta"] != null){
                                $viatico = new stdClass();
                                $viatico->nombre = "Viatico";
                                $viatico->monto = number_format(($v["dias_ruta"] * 12000), 2, ',', '.');;
                                $gastosArreglo[] = $viatico;
                            }
                        //sin de agrega el viatico 


                        $viaje->gastos = $gastosArreglo;


                        // se agregan las rendiciones si tiene
                            $rendicionArreglo = [];

                            $rendicion = $db->createCommand("SELECT * from rendicion r
                            where r.viaje_id = {$v['id']}");
                    
                            $rendicion = $rendicion->queryAll(); 


                            if(count($rendicion) == 1){
                                // $rendicionDetalle = RendicionDetalle::find()->where(["rendicion_id" => ])->orderBy(["fecha_creacion" => SORT_ASC])->all();

                                $rendicionDetalle = $db->createCommand("SELECT rd.*, rm.nombre as motivo
                                from rendicion_detalle rd
                                inner join rendicion_motivo rm on rm.id = rd.motivo_rendicion_id
                                where rd.rendicion_id = {$rendicion[0]['id']}");
                                
                                $rendicionDetalle = $rendicionDetalle->queryAll(); 
                                
                                if(count($rendicionDetalle) > 0){
                                    foreach ($rendicionDetalle as $krd => $vrd) {
                                        $r = new stdClass();
                                        $r->fecha_rendicion = $vrd["fecha_boleta"];
                                        $r->monto = number_format($vrd["monto"], 2, ',', '.');
                                        $r->motivo = $vrd["motivo"]; 
                                        $r->estado = $vrd["validado"] == 0 ? "Sin validar" : "Validado"; 
                                        $rendicionArreglo[] = $r;
                                    }
                                }

                                $viaje->rendiciones = $rendicionArreglo;
                                $viaje->valor_rendido = number_format($rendicion[0]["monto_rendido"] ?? 0, 2, ',', '.');;
                            }

                        // fin se agregan las rendiciones si tiene


                        $viajeArreglo[] = $viaje;

                    }

                    $respuesta->estado = "ok";
                    $respuesta->respuesta = "datos entregados";
                    $respuesta->mensaje = $viajeArreglo;

                }else{
                    $respuesta->estado = "error";
                    $respuesta->respuesta = "No existen viajes rendidos";
                    $respuesta->mensaje = [];
                }
            
                return $respuesta;
                
            }

        //fin viajes con rendicion cerrada por conductor


        //agregar item de rendicion
            public function actionViajesitemrendicion(){

                $this->cabecerasPOST();
                date_default_timezone_set("America/Santiago");
            
                $respuesta = new stdClass();
                if (isset(getallheaders()["Authorization"])) {
                    $token = getallheaders()["Authorization"];
                    $decodeToken = $this->decodeToken($token);
                    if($decodeToken->estado != "ok"){
                        return $this->sendRequest(401, "error", "Token Vencido", ["token vencido"], []);
                    }

                }else{
                    $respuesta->estado = "error";
                    $respuesta->respuesta = "token invalido";
                    $respuesta->mensaje = [];
                    return $respuesta;
                }

            
                if ($_POST) {

                    $_viajeId = isset($_POST["viaje_id"]) ? $_POST["viaje_id"] : null;
                    $_categoriaId = isset($_POST["categoria_id"]) ? $_POST["categoria_id"] : null;
                    $_motivoId = isset($_POST["motivo_id"]) ? $_POST["motivo_id"] : null;
                    $_monto = isset($_POST["monto"]) ? $_POST["monto"] : null;
                    $_tipoDocumentoId = isset($_POST["tipo_documento_id"]) ? $_POST["tipo_documento_id"] : null;
                    $_nroDocumento = isset($_POST["nro_documento"]) ? $_POST["nro_documento"] : null;
                    $_razonSocial = isset($_POST["razon_social"]) ? $_POST["razon_social"] : null;
                    $_rutEmpresa = isset($_POST["rut_empresa"]) ? $_POST["rut_empresa"] : null;
                    $_observacion = isset($_POST["observacion"]) ? $_POST["observacion"] : null;
                    $_fechaBoleta = isset($_POST["fecha_boleta"]) ? $_POST["fecha_boleta"] : null;
                    $_imagen = isset($_POST["imagen"]) ? $_POST["imagen"] : null;

                }else{
                    $respuesta->estado = "error";
                    $respuesta->respuesta = "servicio inaccesible";
                    $respuesta->mensaje = [];
                    return $respuesta;
                }
        
                //validaciones de requeridos
                    $errores = [];

                    if (!isset($_viajeId) || $_viajeId == "" || $_viajeId == null) {
                        $errores[] = 'El campo viaje_id es requerido';
                    }
                    if (!isset($_categoriaId) || $_categoriaId == "" || $_categoriaId == null) {
                        $errores[] = 'El campo categoria_id es requerido';
                    }
                    if (!isset($_motivoId) || $_motivoId == "" || $_motivoId == null) {
                        $errores[] = 'El campo motivo_id es requerido';
                    }
                    if (!isset($_tipoDocumentoId) || $_tipoDocumentoId == "" || $_tipoDocumentoId == null) {
                        $errores[] = 'El campo tipo_documento_id es requerido';
                    }
                    if($_tipoDocumentoId == 2){
                        if (!isset($_nroDocumento) || $_nroDocumento == "" || $_nroDocumento == null) {
                            $errores[] = 'El campo nro_documento es requerido';
                        }
                        if (!isset($_razonSocial) || $_razonSocial == "" || $_razonSocial == null) {
                            $errores[] = 'El campo razon_social es requerido';
                        }
                        if (!isset($_rutEmpresa) || $_rutEmpresa == "" || $_rutEmpresa == null) {
                            $errores[] = 'El campo rut_empresa es requerido';
                        }
                    }

                    if (!isset($_fechaBoleta) || $_fechaBoleta == "" || $_fechaBoleta == null) {
                        $errores[] = 'El campo fecha_boleta es requerido';
                    }
                    if (!isset($_imagen) || $_imagen == "" || $_imagen == null) {
                        $errores[] = 'El campo imagen es requerido';
                    }
        
                    if (count($errores) > 0) {
                        $respuesta->estado = "error";
                        $respuesta->respuesta =  "detalle errores";
                        $respuesta->mensaje = $errores;
                        return $respuesta;
                    }
                //fin validaciones de requeridos


                $rendicion = Rendicion::find()->where(["viaje_id" =>  $_viajeId])->one();
                $rendicionId = 0;
                if($rendicion){
                    $rendicionId = $rendicion->id;

                    $rendicion->fecha_edicion = date("Y-m-d H:i:s");
                    $rendicion->save();
                }else{
                    $rendicionNueva = new Rendicion();
                    $rendicionNueva->viaje_id = $_viajeId;

                    $viajeInfo = Viajes::find()->where(["id" => $_viajeId])->one();
                    $rendicionNueva->monto_saldo = $viajeInfo->monto_aprovisionado * -1;
                    $rendicionNueva->estado = 0;
                    $rendicionNueva->monto_aprovisionado = $viajeInfo->monto_aprovisionado;
                    $rendicionNueva->fecha_creacion = date("Y-m-d");
                    $rendicionNueva->estado = 0;
                    if($rendicionNueva->save()){
                        $rendicionId = $rendicionNueva->id;
                    }
                }                
                
                $db = Yii::$app->get("db");
                $transaction = $db->beginTransaction();
                try {
                    $rendicionDetalle = new RendicionDetalle();
                    $rendicionDetalle->rendicion_id = $rendicionId;
                    $rendicionDetalle->categoria_rendicion_id = $_categoriaId;
                    $rendicionDetalle->motivo_rendicion_id = $_motivoId;
                    $rendicionDetalle->monto = str_replace(",", ".", str_replace(".", "", $_monto));
                    $rendicionDetalle->tipo_documento_id = $_tipoDocumentoId;
                    $rendicionDetalle->tipo_ingreso_id = 3;
                    $rendicionDetalle->nro_documento = $_nroDocumento;
                    $rendicionDetalle->razon_social = $_razonSocial;
                    $rendicionDetalle->razon_social = $_razonSocial;
                    $rendicionDetalle->rut_empresa = $_rutEmpresa;
                    $rendicionDetalle->observacion = $_observacion;
                    $rendicionDetalle->fecha_boleta = date_format(date_create($_fechaBoleta),'Y-m-d H:i');
                    $rendicionDetalle->fecha_creacion = date("Y-m-d H:i:s");


                    //  validar fotos
                        $fotos =  '';
                        
                        $ii = 0;

                        if(gettype($_imagen) == "string"){
                            $_imagen = explode("|", $_imagen);
                        }

                        $arregloFotos = [];
                        foreach ($_imagen as $fp => $foto) {

                            //se hace un split a la cadena en , para tomar solo la imagen
                            $base64_string = explode(",",  $foto);
            
                            //se crear una imagen desde el base 64
                            $foto = imagecreatefromstring(base64_decode($base64_string[1]));
                            
                            //se guarda el nombre de la imagen
                            $nombrefoto = 'rendicion_'.$rendicionId.'_'.date("Ymdhis").$ii.'.png';
            
                            // si el directorio no esta creado se crea
                            if(!is_dir('../../sgv/web/documentos/viajes/rendicion/'.$_viajeId)) {
                                mkdir('../../sgv/web/documentos/viajes/rendicion/'.$_viajeId);
                            }
                            
                            //se guarda la iamgen en el directorio correspondiente
                            if (imagepng($foto, Yii::getAlias('../../sgv/web/documentos/viajes/rendicion/'.$_viajeId.'/').$nombrefoto, 9)) {
                                $arregloFotos[] = $nombrefoto;
                                // $fotos .= $nombrefoto.',';
                            }else{
                                $transaction->rollback();
                                $respuesta->estado = "error";
                                $respuesta->respuesta = "ocurrio un error al guardar la imagen";
                                $respuesta->mensaje = [];  
                                return $respuesta;
                            }
            
                            $ii++;
                        }
                    //  fin validar fotos

                    $rendicionDetalle->foto = implode(",", $arregloFotos); //$fotos;
    
                    $rendicionDetalle->validado = 0;

                    if($rendicionDetalle->save()){
                        $transaction->commit();
                        $respuesta->estado = "ok";
                        $respuesta->respuesta = "Rendición agregada con éxito";
                        $respuesta->mensaje = [];          

                    }else{
                        $transaction->rollback();

                        $respuesta->estado = "error";
                        $respuesta->respuesta = "ocurrio un error al guardar el item de rendicion";
                        $respuesta->mensaje = [];  
                    }

                } catch(Exception $e) {
                    $transaction->rollback();
                    $respuesta->estado = "error";
                    $respuesta->respuesta = "ocurrio un error al iniciar la transaccion";
                    $respuesta->mensaje = [];  
                }

                return $respuesta;
                
            }

        //fin agregar item de rendicion


    // /////////////////////////////////////////////////// FIN VIAJES ////////////////////////////////////////////



    // ////////////////////////////////////////////////// PARADAS ////////////////////////////////////////////////

        //detalle de una parada
            public function actionDetalleparada(){

                try {
                    $this->cabecerasGET();
    
                    date_default_timezone_set("America/Santiago");
    
                    $respuesta = new stdClass();
                    if (isset(getallheaders()["Authorization"])) {
                        $token = getallheaders()["Authorization"];
                        $decodeToken = $this->decodeToken($token);
                        if($decodeToken->estado != "ok"){
                            return $this->sendRequest(401, "error", "Token Vencido", ["token vencido"], []);
                        }
    
                    }else{
                        $error = "Token Invalido";
                        return $this->sendRequest(401, "error", $error, [$error], []);
                    }
    
                    // $key = $this->validarKey(getallheaders()["Autorizacion"]);
    
                    
                    // if ($key != null) {
                
                    if ($_GET) {
                        $error = "Servicio Inaccesible";
                        return $this->sendRequest(405, "error", $error, [$error], []);
                    }else{
                        $post = file_get_contents('php://input');
                        $data = json_decode($post);
            
                        $_idParada = isset($data->id_parada) ? $data->id_parada : null;
                        $_subdominio = isset($data->subdominio) ? $data->subdominio : null;
                    }
            
                    //validaciones de requeridos
                        $errores = [];
    
                        if (!isset($_idParada) || $_idParada =="" || $_idParada == null) {
                            $errores[] = 'El campo id_parada es requerido';
                        }
                        if (!isset($_subdominio) || $_subdominio == "" || $_subdominio == null) {
                            $errores[] = 'El subdominio es requerido';
                        }
            
                        if (count($errores) > 0) {
                            return $this->sendRequest(400, "error", "Campos Requeridos", [$errores], []);
                        }
                    //fin validaciones de requeridos
    
                    $asignarBD = Yii::$app->bermann->asignarBD($_subdominio);
                    if(!$asignarBD->asignada){
                        $error = "Subdominio invalido";
                        return $this->sendRequest(400, "error", $error, [$error], []);
                    }
    
    
                    $detalleParada = ViajeDetalle::findOne($_idParada);
    
                    if($detalleParada){
                        $datos =  new stdClass();
    
                        $datos->id_parada = $detalleParada->id;
                        $datos->viaje_id = $detalleParada->viaje_id;
                        $datos->nro_viaje = $detalleParada->viaje->nro_viaje;
                        $datos->cliente = $detalleParada->viaje->cliente->nombre_fantasia;
                        $datos->zona = $detalleParada->zona->nombre;
                        $datos->fecha_presentacion = $detalleParada->viaje->fecha_presentacion;
                        switch ($detalleParada->estado) {
                            case 0:
                                $datos->estado = "Sin Eventos a tiempo";
                                break;
                            case 1:
                                $datos->estado = "Sin Eventos atrasado";
                                break;
                            case 2:
                                $datos->estado = "Con Eventos a tiempo";
                                break;
                            case 3:
                                $datos->estado = "Con Eventos atrasado";
                                break;
                        }
    
                        $datos->fecha_planificacion_entrada = "";
                        if($detalleParada->fecha_entrada != null){
                            $datos->fecha_planificacion_entrada = \Datetime::createFromFormat("Y-m-d H:i:s", $detalleParada->fecha_entrada)->format("d/m/Y H:i:s");
                        }
                        $datos->fecha_planificacion_salida = "";
                        if($detalleParada->fecha_salida != null){
                            $datos->fecha_planificacion_salida = \Datetime::createFromFormat("Y-m-d H:i:s", $detalleParada->fecha_salida)->format("d/m/Y H:i:s");
                        }
                        $datos->fecha_real_entrada = "";
                        if($detalleParada->fecha_entrada_gps != null){
                            $datos->fecha_real_entrada = \Datetime::createFromFormat("Y-m-d H:i:s", $detalleParada->fecha_entrada_gps)->format("d/m/Y H:i:s");
                        }
                        $datos->fecha_real_salida = "";
                        if($detalleParada->fecha_salida_gps != null){
                            $datos->fecha_real_salida = \Datetime::createFromFormat("Y-m-d H:i:s", $detalleParada->fecha_salida_gps)->format("d/m/Y H:i:s");
                        }
    
                        // $datos->boton_llegue = 0;
                        // if($detalleParada->fecha_entrada_gps == null){
                        //     $datos->boton_llegue = 1;
                        // }
                        
                        $datos->estado_rampla = "desenganchada";
                        $datos->rampla = "";
    
                        $viajeDetalleRampla = ViajeDetalleRampla::find()->where(["viaje_detalle_id" => $_idParada])->orderBy(["id" => SORT_DESC])->one();
                        
                        if($viajeDetalleRampla){
                            
                            if($viajeDetalleRampla->estado_rampla_id == 2){
                                $datos->rampla = $viajeDetalleRampla->fecha_error == null ? $viajeDetalleRampla->rampla : $viajeDetalleRampla->rampla_correcta;
                                $datos->estado_rampla = "enganchada";
                                $datos->fecha_engache = $viajeDetalleRampla->fecha_error == null ? $viajeDetalleRampla->fecha_creacion : $viajeDetalleRampla->fecha_error;
                            }else{
                                $datos->estado_rampla = "desenganchada";
                                $datos->rampla = "";
                                $datos->fecha_desengache = $viajeDetalleRampla->fecha_creacion;
                            }
    
                        }else{
                            $datos->estado_rampla = "por enganchar";
                            $datos->rampla = "";
                        }
    
                        // $datos->estado_accion =  $datos->accion =$datos->fecha_accion = $datos->latitud = $datos->longitud = null;
    
                        $detalleAccion = ViajeDetalleAccion::find()->where(["viaje_detalle_id" => $_idParada])->orderBy(["id" => SORT_DESC])->one();
    
                        $datos->accion = "";
                        $datos->fecha_accion = "";
                        $datos->latitud = "";
                        $datos->longitud = "";
                        if($detalleAccion){
                            $datos->accion = $detalleAccion->accion->nombre;
                            $datos->fecha_accion = $detalleAccion->fecha;
                            $datos->latitud = $detalleAccion->latitud;
                            $datos->longitud = $detalleAccion->longitud;
                        }
    
                        $datos->estimado = "";
                        $datos->hora_estimada_salida = "";
                        $datos->corregido = "";
                        $datos->tiempo_corregido_salida = "";
    
                        return $this->sendRequest(200, "OK", "Datos entregados", [], $datos);
                    }else{
                        $error = "No hay detalle para la parada ingresada";
                        return $this->sendRequest(400, "error", $error, [$error], []);
                    }
                } catch (\Throwable $th) {
                    $error = $th->getMessage();
                    return $this->sendRequest(500, "error", "Ha ocurrido un error en el servidor al procesar la solicitud", [$error], []);
                }
            }
        //fin detalle de una parada

    ////////////////////////////////////////////////////// FIN PARADAS ////////////////////////////////////////////

    // /////////////////////////////////////////////////// ACCIONES ////////////////////////////////////////////

    
        //estados de pod
            public function actionEstadospod(){
                
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
                        $error = "Token Invalido";
                        return $this->sendRequest(401, "error", $error, [$error], []);
                    }
    
                    if ($_GET) {
                        $error = "Servicio Inaccesible";
                        return $this->sendRequest(405, "error", $error, [$error], []);
                    }else{
                        $post = file_get_contents('php://input');
                        $data = json_decode($post);
                        $_subdominio = isset($data->subdominio) ? $data->subdominio : null;
                    }
            
                    //validaciones de requeridos
                        $errores = [];
    
                        if (!isset($_subdominio) || $_subdominio == "" || $_subdominio == null) {
                            $errores[] = 'El subdominio es requerido';
                        }
            
                        if (count($errores) > 0) {
                            return $this->sendRequest(400, "error", "Campos Requeridos", [$errores], []);
                        }
                    //fin validaciones de requeridos
    
    
                    $asignarBD = Yii::$app->bermann->asignarBD($_subdominio);
                    if(!$asignarBD->asignada){
                        $error = "Subdominio invalido";
                        return $this->sendRequest(400, "error", $error, [$error], []);
                    }
    
                    $respuesta = new stdClass();
                            
                    $estatusPOD  = EstatusPod::find()->all();
    
                    if(count($estatusPOD) > 0){
    
                        $estadosPOD = [];
                        foreach ($estatusPOD as $key => $value) {
                            $datos =  new stdClass();
                            $datos->id = $value->id;
                            $datos->nombre = $value->nombre;
                            $estadosPOD[] = $datos;
                        }
                        return $this->sendRequest(200, "ok", "Datos Entregados", [], $estadosPOD);
    
                    }else{
                        $error = "No existen estados para POD";
                        return $this->sendRequest(404, "ok", $error, [$error], []);
                    }
                } catch (\Throwable $th) {
                    $error = $th->getMessage();
                    return $this->sendRequest(500, "error", "Ha ocurrido un error en el servidor al procesar la solicitud", [$error], []);
                }
                
            }
        //fin estados de pod

        // agregar POD
            public function actionAgregarpod(){
                
                
                
                try {
                    $this->cabecerasPOST();
            
                    date_default_timezone_set('America/Santiago');
    
                    $respuesta = new stdClass();
                    if (isset(getallheaders()["Authorization"])) {
                        $token = getallheaders()["Authorization"];
                        $decodeToken = $this->decodeToken($token);
                        if($decodeToken->estado != "ok"){
                            return $this->sendRequest(401, "error", "Token Vencido", ["token vencido"], []);
                        }
    
                    }else{
                        $error = "Token Invalido";
                        return $this->sendRequest(401, "error", $error, [$error], []);
                    }
    
                    // if ($key != null) {
            
                    if ($_POST) {
                        $error = "Servicio Inaccesible";
                        return $this->sendRequest(405, "error", $error, [$error], []);
                        
                    }else{
                        $post = file_get_contents('php://input');
                        $data = json_decode($post);
    
                        $_idViaje = isset($data->id_viaje) ? $data->id_viaje : null;
                        $_idParada  = isset($data->id_parada) ? $data->id_parada : null;
                        $_fotos = isset($data->fotos) ? $data->fotos : null;
                        $_nombreFirma = isset($data->nombre_firma) ? $data->nombre_firma : null;
                        $_rutFirma = isset($data->rut_firma) ? $data->rut_firma : null;
                        $_empresaFirma = isset($data->empresa_firma) ? $data->empresa_firma : null;
                        // $_firma = isset($data->firma) ? $data->firma : null;
                        $_estatusPod = isset($data->estatus_pod) ? $data->estatus_pod : null;
                        $_subdominio = isset($data->subdominio) ? $data->subdominio : null;
            
                    }
                    
                    //validaciones de requeridos
                        $errores = [];
    
                        if (!isset($_idViaje) || $_idViaje == "" || $_idViaje == null) {
                            $errores[] = 'El campo id_viaje es requerido';
                        }
                        if (!isset($_idParada) || $_idParada =="" || $_idParada == null) {
                            $errores[] = 'El campo id_parada es requerido';
                        }
    
    
                        if (!isset($_fotos) || $_fotos == "" || $_fotos == null) {
                            $errores[] = 'El campo fotos es requerido';
                        }
                        
    
                        if (!isset($_estatusPod) || $_estatusPod == "" || $_estatusPod == null) {
                            $errores[] = 'El campo estatus_pod es requerido';
                        }else{
                            if($_estatusPod < 0 || $_estatusPod > 4){
                                $errores[] = 'Los estados permitidos son 1,2,3,4';
                            }
                        }
    
                        if (!isset($_subdominio) || $_subdominio == "" || $_subdominio == null) {
                            $errores[] = 'El campo subdominio es requerido';
                        }
            
                        if (count($errores) > 0) {
                            return $this->sendRequest(400, "error", "Campos Requeridos", [$errores], []);
                        }
                    //fin validaciones de requeridos
    
                    

                } catch (\Throwable $th) {
                    $error = $th->getMessage();
                    return $this->sendRequest(500, "error", "Ha ocurrido un error en el servidor al procesar la solicitud", [$error], []);
                }


                try {
                    $asignarBD = Yii::$app->bermann->asignarBD($_subdominio);
                    if(!$asignarBD->asignada){
                        $error = "Subdominio invalido";
                        return $this->sendRequest(400, "error", $error, [$error], []);
                    }
                    
                    $db = Yii::$app->get('db', Yii::$app->db);
    
                    $transaction = $db->beginTransaction();
                    
                    $viajeID = $_idViaje;
    
                    $viajePod = ViajePod::find()->where(["viaje_id" => $viajeID])->one();
    
                    $pruebaEntrega = new ViajeDetallePod();
                    if(!$viajePod){
    
                        $viajePod->viaje_id = $viajeID;
                        $viajePod->estatus_pod_id =  5;
                        $viajePod->nombre_firma = $_nombreFirma;
                        $viajePod->rut_firma = $_rutFirma;
                        $viajePod->empresa_firma = $_empresaFirma;
                        $viajePod->fecha_creado =  date("Y-m-d H:i:s");
                        if($viajePod->save()){
                            $transaction->rollback();
                            $error = "Ha ocurrido un error al guardar POD";
                            return $this->sendRequest(400, "error", $error, [$error], []);
                        }
                    }
                    
                    //  validar fotos
    
                        $i = 0;
                        foreach ($_fotos as $fp => $foto) {
                            
                            //se hace un split a la cadena en , para tomar solo la imagen
                            $base64_string = explode(",",  $foto);
            
                            //se crear una imagen desde el base 64
                            $foto = imagecreatefromstring(base64_decode($base64_string[1]));
            
                            // si el directorio no esta creado se crea
                            if(!is_dir(Yii::getAlias('@webroot/documentos/viajes/pod/'.$viajeID))) {
                                mkdir(Yii::getAlias('@webroot/documentos/viajes/pod/'.$viajeID));
                            }
                            
                            //se guarda el nombre de la imagen
                            $nombrefoto = 'pod_'.$_idParada.'_'.date("Ymdhis").$i.'.png';
                            //se guarda la iamgen en el directorio correspondiente
                            if (imagepng($foto, Yii::getAlias('@webroot/documentos/viajes/pod/'.$viajeID.'/').$nombrefoto, 9)) {
    
                                $viajeDetallPod = new ViajeDetallePod();
    
                                $viajeDetallPod->viaje_pod_id = $viajePod->id;
                                $viajeDetallPod->viaje_detalle_id = $_idParada;
                                $viajeDetallPod->foto = $nombrefoto;
                                $viajeDetallPod->estatus_pod_id = $_estatusPod;
                                if(!$viajeDetallPod->save()){
                                    $transaction->rollback();
                                    $error = "Ha ocurrido un error al guardar el detalle del POD";
                                    return $this->sendRequest(400, "error", $error, [$error], []);
                                }
                            }
    
                            $i++;
                        }
    
                    // fin validar fotos


                    //******************************************************** */ insertar log y auditoria    

                    $transaction->commit();                    
                    return $this->sendRequest(200, "ok", "Se agregó POD con éxito", [], []);

                } catch (\Throwable $th) {
                    $transaction->rollback();
                    $error = $th->getMessage();
                    return $this->sendRequest(500, "error", "Ha ocurrido un error en el servidor al procesar la solicitud", [$error], []);
                }
                    

                
                // $viajeDetalle = ViajeDetalle::find()->where(["id" => $_idParada, "viaje_id" => $viajeID])->one();
                // $pruebaEntrega = new ViajeDetallePod();
                // $pruebaEntrega->viaje_detalle_id = $viajeDetalle->id;
                // $pruebaEntrega->nombre_firma = $_nombreFirma;
                // $pruebaEntrega->rut_firma = $_rutFirma;
                // $pruebaEntrega->empresa_firma = $_empresaFirma;
                // $pruebaEntrega->estatus_pod_id = $_estatusPod;
                // $pruebaEntrega->fecha_creado = date("Y-m-d H:i:s");

                // if ($pruebaEntrega->save()) {

                //     // se envio el POD a onesigth
                //     $resPOD = $this->enviarPodOS($viajeID);

                //     //  validar fotos

                //         $ii = 0;
                //         foreach ($_fotos as $fp => $foto) {
                            
                //             //se hace un split a la cadena en , para tomar solo la imagen
                //             $base64_string = explode(",",  $foto);
            
                //             //se crear una imagen desde el base 64
                //             $foto = imagecreatefromstring(base64_decode($base64_string[1]));
            
                //             // si el directorio no esta creado se crea
                //             if(!is_dir(Yii::getAlias('@webroot/documentos/viajes/pod/'.$viajeID))) {
                //                 mkdir(Yii::getAlias('@webroot/documentos/viajes/pod/'.$viajeID));
                //             }
                            
                //             //se guarda el nombre de la imagen
                //             $nombrefoto = 'pod_'.$_idParada.'_'.date("Ymdhis").$ii.'.png';
                //             //se guarda la iamgen en el directorio correspondiente
                //             if (imagepng($foto, Yii::getAlias('@webroot/documentos/viajes/pod/'.$viajeID.'/').$nombrefoto, 9)) {

                //                 $viajePodDetalleImagenes = new ViajeDetallePodDetalle();

                //                 $viajePodDetalleImagenes->viaje_detalle_pod_id = $pruebaEntrega->id;
                //                 $viajePodDetalleImagenes->foto = $nombrefoto;
                //                 $viajePodDetalleImagenes->validado = 0;
                //                 $viajePodDetalleImagenes->save();
                //             }

                //             $ii++;
                //         }

                //     // fin validar fotos

                //     // $resPOD = json_decode($resPOD["respone"], true);

                //     // $this->insertarLogViajes($viajeID, "{}", "Se agrego desde APP movil", $viajeDetalle->id)
                //     // if($resPOD["status"] == 200){
                //     //     $this->insertarLogViajes($viajeID, "{}", "Se agrego POD a OneSight", $viajeDetalle->id)
                //     // }

                        

            }
        // fin agregar POD

        // listados de POD por viaje
            public function actionListadopodporviaje(){

                try {
                    $this->cabecerasGET();
    
                    date_default_timezone_set('America/Santiago');
    
                    $respuesta = new stdClass();
                    if (isset(getallheaders()["Authorization"])) {
                        $token = getallheaders()["Authorization"];
                        $decodeToken = $this->decodeToken($token);
                        if($decodeToken->estado != "ok"){
                            return $this->sendRequest(401, "error", "Token Vencido", ["token vencido"], []);
                        }
    
                    }else{
                        $error = "Token Invalido";
                        return $this->sendRequest(401, "error", $error, [$error], []);
                    }
    
                    if ($_GET) {
                        $error = "Servicio Inaccesible";
                        return $this->sendRequest(405, "error", $error, [$error], []);
            
                    }else{
                        $post = file_get_contents('php://input');
                        $data = json_decode($post);
            
                        $_viajeId = isset($data->id_viaje) ? $data->id_viaje : null;
                        $_subdominio = isset($data->subdominio) ? $data->subdominio : null;
            
                    }
    
    
                    $errores = [];
            
                    if (!isset($_viajeId) || $_viajeId == "" || $_viajeId == null) {
                        $errores[] = 'El campo id_viaje es requerido';
                    }
                    if (!isset($_subdominio) || $_subdominio == "" || $_subdominio == null) {
                        $errores[] = 'El campo subdominio es requerido';
                    }
        
                    if (count($errores) > 0) {
                        return $this->sendRequest(400, "error", "Campos Requeridos", [$errores], []);
                    }
    
    
                    $asignarBD = Yii::$app->bermann->asignarBD($_subdominio);
                    if(!$asignarBD->asignada){
                        $error = "Subdominio invalido";
                        return $this->sendRequest(400, "error", $error, [$error], []);
                    }
    
    
                    $viaje = Viajes::find()->where(["id" => $_viajeId])->one();
    
                    if($viaje){
    
                        $viajeDetalle = ViajeDetalle::find()->where(["viaje_id" => $viaje->id])->orderBy(["orden" => SORT_ASC])->all();
    
                        if(count($viajeDetalle)>0){
    
                            $viajeDetalleId = [];
    
                            foreach ($viajeDetalle as $kvd => $vvd) {
                                $viajeDetalleId[] = $vvd->id;
                            }
                            $viaje_pod = ViajeDetallePod::find()->where(["IN", "viaje_detalle_id", $viajeDetalleId])->orderBy(["id" => SORT_ASC])->all();
                            
                            if(count($viaje_pod) > 0){
                                $datos = [];
    
                                // $url = str_replace("apidoc", "images", $_SERVER["HTTP_REFERER"]);
                                foreach ($viaje_pod as $key => $value) {
                                    $viajePod = new stdClass();
                                    $viajePod->id = $value->id;
                                    $viajePod->viaje_id = $viaje->id;
                                    $viajePod->hr = $viaje->hojaRuta->nro_hr;
                                    $viajePod->viaje_detalle_id = $value->viaje_detalle_id;
                                    $viajePod->zona = $value->viajeDetalle->zona->nombre;
                                    
                                    $viajePodDetalleImagenes = ViajeDetallePodDetalle::find()->where(["IN", "viaje_detalle_pod_id", $value->id])->orderBy(["id" => SORT_ASC])->all();
                                    $arregloFotos = [];
                                    foreach ($viajePodDetalleImagenes as $kvpdi => $vpdi) {
                                        $arregloFotos[$kvpdi]["imagen"] = $asignarBD->urlRecursosExternos."documentos/viajes/pod/".$viaje->id."/".$vpdi->foto;
                                        $arregloFotos[$kvpdi]["estado"] = $vpdi->validado;
                                    }

                                    $viajePod->fotos = $arregloFotos;
                                    $viajePod->nombre_firma = $value->nombre_firma;
                                    $viajePod->rut_firma = $value->rut_firma;
                                    $viajePod->empresa_firma = $value->empresa_firma;
                                    $viajePod->estatus_pod = $value->estatusPod->nombre;
                                    $viajePod->fecha_creado = $value->fecha_creado;
                                    $datos[] = $viajePod;
                                }
    
                                // var_dump($_SERVER);exit;
                                return $this->sendRequest(200, "ok", "Datos entregados", [], $datos);
                            }else{
                                $error = "El viaje no tiene POD asociadas";
                                return $this->sendRequest(404, "ok", $error, [$error], []);
                            }
                        }else{
                            $error = "Error al obtener POD del viaje";
                            return $this->sendRequest(400, "error", $error, [$error], []);
                        }
                        
                    }else{
                        $error = "El viaje no existe";
                        return $this->sendRequest(404, "error", $error, [$error], []);
                    }
                } catch (\Throwable $th) {
                    $error = $th->getMessage();
                    return $this->sendRequest(500, "error", "Ha ocurrido un error en el servidor al procesar la solicitud", [$error], []);
                }
            }
        // fin listados de POD por viaje

        // agregar novedades
            public function actionAgregarnovedad(){

                try {
                    $this->cabecerasPOST();
                    
                    date_default_timezone_set('America/Santiago');
    
                    $respuesta = new stdClass();
                    if (isset(getallheaders()["Authorization"])) {
                        $token = getallheaders()["Authorization"];
                        $decodeToken = $this->decodeToken($token);
                        if($decodeToken->estado != "ok"){
                            return $this->sendRequest(401, "error", "Token Vencido", ["token vencido"], []);
                        }
    
                    }else{
                        $error = "Token Invalido";
                        return $this->sendRequest(401, "error", $error, [$error], []);
                    }
    
                
                    if ($_POST) {
    
                        $error = "Servicio Inaccesible";
                        return $this->sendRequest(405, "error", $error, [$error], []);
                        
                    }else{
                        $post = file_get_contents('php://input');
                        $data = json_decode($post);
    
                        $_viajeId = isset($data->id_viaje) ? $data->id_viaje : null;
                        $_paradaId  = isset($data->id_parada) ? $data->id_parada : null;
                        $_subestatusViajeId = isset($data->subestatus_viaje_id) ? $data->subestatus_viaje_id : null;
                        $_fotos = isset($data->fotos) ? $data->fotos : null;
                        $_observaciones = isset($data->observaciones) ? $data->observaciones : null;
                        $_subdominio = isset($data->subdominio) ? $data->subdominio : null;
            
                    }
                    
                    //validaciones de requeridos
                        $errores = [];
    
                        if (!isset($_viajeId) || $_viajeId == "" || $_viajeId == null) {
                            $errores[] = 'El campo nro_viaje es requerido';
                        }
                        if (!isset($_paradaId) || $_paradaId =="" || $_paradaId == null) {
                            $errores[] = 'El campo id_parada es requerido';
                        }
                        if (!isset($_subestatusViajeId) || $_subestatusViajeId == "" || $_subestatusViajeId == null) {
                            $errores[] = 'El campo subestatus_viaje_id es requerido';
                        }
                        
                        if (!isset($_subdominio) || $_subdominio == "" || $_subdominio == null) {
                            $errores[] = 'El campo subdominio es requerido';
                        }
            
                        if (count($errores) > 0) {
                            return $this->sendRequest(400, "error", "Campos Requeridos", [$errores], []);
                        }
                    //fin validaciones de requeridos
    
    
                    $asignarBD = Yii::$app->bermann->asignarBD($_subdominio);
                    if(!$asignarBD->asignada){
                        $error = "Subdominio invalido";
                        return $this->sendRequest(400, "error", $error, [$error], []);
                    }

                    //  validar fotos
    
                        
                        $fotos =  '';
                        
                        $ii = 0;
                        if(isset($_fotos) && count($_fotos) > 0 ){
                            foreach ($_fotos as $fp => $foto) {
        
                                //se hace un split a la cadena en , para tomar solo la imagen
                                $base64_string = explode(",",  $foto);
                
                                //se crear una imagen desde el base 64
                                $foto = imagecreatefromstring(base64_decode($base64_string[1]));
                
                                // si el directorio no esta creado se crea
                                if(!is_dir(Yii::getAlias('@webroot/documentos/viajes/novedades/'.$_viajeId))) {
                                    mkdir(Yii::getAlias('@webroot/documentos/viajes/novedades/'.$_viajeId));
                                }
                                
                                //se guarda el nombre de la imagen
                                $nombrefoto = 'novedades_'.$_paradaId.'_'.date("Ymdhis").$ii.'.png';
                                //se guarda la iamgen en el directorio correspondiente
                                if (imagepng($foto, Yii::getAlias(Yii::getAlias('@webroot/documentos/viajes/novedades/'.$_viajeId.'/').$nombrefoto, 9))) {
                                    $fotos .= $nombrefoto.',';
                                }
                
                                $ii++;
                            }
                        }
    
                    // fin validar fotos
    
    
                    $viajeDetalle = ViajeDetalle::find()->where(["id" => $_paradaId, "viaje_id" => $_viajeId])->one();
    
                    $viaje_novedades = new ViajeNovedades();
        
                    $viaje_novedades->viaje_detalle_id = $viajeDetalle->id;
                    $viaje_novedades->subestatus_viaje_id = $_subestatusViajeId;
                    // $viaje_novedades->substatus_viaje_motivo_id = $subestatusMotivoId;
                    $viaje_novedades->fotos = trim($fotos,',');
                    $viaje_novedades->observaciones = $_observaciones;
                    $viaje_novedades->fecha_creacion = date("Y-m-d H:i:s");
    
                    if ($viaje_novedades->save()) {
    
                        $viaje = Viajes::find()->where(["id" => $_viajeId])->one();
                        $viaje->subestatus_viaje_id = $viaje_novedades->subestatus_viaje_id;
                        $viaje->save();
    
                        //******************************************************** */ insertar log y auditoria
                        return $this->sendRequest(200, "ok", "Se agregó Novedad con éxito", [], []);
                    }else{
                        $error = "Ha ocurrido un error al guardar la novedad";
                        return $this->sendRequest(400, "error", $error, [$error], []);
                    }
                } catch (\Throwable $th) {
                    $error = $th->getMessage();
                    return $this->sendRequest(500, "error", "Ha ocurrido un error en el servidor al procesar la solicitud", [$error], []);
                }

            }
        // fin agregar novedades



        //listado de estados de novedades
            public function actionEstadosnovedades(){

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
                        $error = "Token Invalido";
                        return $this->sendRequest(401, "error", $error, [$error], []);
                    }
    
                    $respuesta = new stdClass();
    
    
                    if ($_GET) {
                        $error = "Servicio Inaccesible";
                        return $this->sendRequest(405, "error", $error, [$error], []);
            
                    }else{
                        $post = file_get_contents('php://input');
                        $data = json_decode($post);
            
                        $_subdominio = isset($data->subdominio) ? $data->subdominio : null;
                    }
    
                    $errores = [];
    
                    if (!isset($_subdominio) || $_subdominio == "" || $_subdominio == null) {
                        $errores[] = 'El campo subdominio es requerido';
                    }
        
                    if (count($errores) > 0) {
                        return $this->sendRequest(400, "error", "Campos Requeridos", [$errores], []);
                    }
    
                    $asignarBD = Yii::$app->bermann->asignarBD($_subdominio);
                    if(!$asignarBD->asignada){
                        $error = "Subdominio invalido";
                        return $this->sendRequest(400, "error", $error, [$error], []);
                    }
       
                    $estatusNovedades  = SubestatusViaje::find()->where(["fecha_borrado" => null])->all();
    
                    if(count($estatusNovedades) > 0){
    
                        $subEstadosNovedades = [];
                        foreach ($estatusNovedades as $key => $value) {
                            $datos =  new stdClass();
                            $datos->id = $value->id;
                            $datos->nombre = $value->nombre;
                            $subEstadosNovedades[] = $datos;
                        }
    
                        return $this->sendRequest(200, "ok", "Datos entregados", [], $subEstadosNovedades);
                    }else{
                        $error = "No existen estados para novedades";
                        return $this->sendRequest(404, "ok", $error, [$error], []);
                    }
                } catch (\Throwable $th) {
                    $error = $th->getMessage();
                    return $this->sendRequest(500, "error", "Ha ocurrido un error en el servidor al procesar la solicitud", [$error], []);
                }
            }         
        // fin listado de estados de novedades

        
        // listado de novedades por viaje
            public function actionListadonovedadesporviaje(){
                try {
                    $this->cabecerasGET();

                    date_default_timezone_set('America/Santiago');

                    $respuesta = new stdClass();
                    if (isset(getallheaders()["Authorization"])) {
                        $token = getallheaders()["Authorization"];
                        $decodeToken = $this->decodeToken($token);
                        if($decodeToken->estado != "ok"){
                            return $this->sendRequest(401, "error", "Token Vencido", ["token vencido"], []);
                        }
                    }else{
                        $error = "Token Invalido";
                        return $this->sendRequest(401, "error", $error, [$error], []);
                    }


                    // if ($key != null) {

                    if ($_GET) {
                        $error = "Servicio Inaccesible";
                        return $this->sendRequest(405, "error", $error, [$error], []);
            
                    }else{
                        $post = file_get_contents('php://input');
                        $data = json_decode($post);
            
                        $_viajeId = isset($data->id_viaje) ? $data->id_viaje : null;
                        $_subdominio = isset($data->subdominio) ? $data->subdominio : null;
                    }


                    $errores = [];
            
                    if (!isset($_viajeId) || $_viajeId == "" || $_viajeId == null) {
                        $errores[] = 'El campo id_viaje es requerido';
                    }

                    if (!isset($_subdominio) || $_subdominio == "" || $_subdominio == null) {
                        $errores[] = 'El campo subdominio es requerido';
                    }
        
                    if (count($errores) > 0) {
                        return $this->sendRequest(400, "error", "Campos Requeridos", [$errores], []);
                    }

                    $asignarBD = Yii::$app->bermann->asignarBD($_subdominio);
                    if(!$asignarBD->asignada){
                        $error = "Subdominio invalido";
                        return $this->sendRequest(400, "error", $error, [$error], []);
                    }
            

                    $viaje = Viajes::findOne($_viajeId);

                    if($viaje){
                        $viajeDetalle = ViajeDetalle::find()->where(["viaje_id" => $viaje->id])->orderBy(["orden" => SORT_ASC])->all();

                        if(count($viajeDetalle)>0){

                            $viajeDetalleId = [];

                            foreach ($viajeDetalle as $kvd => $vvd) {
                                $viajeDetalleId[] = $vvd->id;
                            }
                            $viaje_novedades = ViajeNovedades::find()->where(["IN", "viaje_detalle_id", $viajeDetalleId])->all();
                            
                            if(count($viaje_novedades) > 0){
                                $datos = [];

                                // $url = str_replace("apidoc", "images", $_SERVER["HTTP_REFERER"]);
                                foreach ($viaje_novedades as $key => $value) {
                                    $viajeNovedades = new stdClass();
                                    $viajeNovedades->id = $value->id;
                                    $viajeNovedades->viaje_id = $viaje->id;
                                    $viajeNovedades->hr = $viaje->hojaRuta->nro_hr;
                                    $viajeNovedades->subestatus_viaje_id = $value->subestatusViaje->nombre;
                                    $fotos = explode(",", $value->fotos);
                                    $fotosArr = [];

                                    foreach ($fotos as $kf => $vf) {
                                        if($vf != ""){
                                            $fotosArr[] = $asignarBD->urlRecursosExternos."documentos/viajes/novedades/".$viaje->id."/".$vf;
                                        }
                                    }

                                    $viajeNovedades->fotos = $fotosArr;
                                    $viajeNovedades->observaciones = $value->observaciones;
                                    $viajeNovedades->fecha_creacion = $value->fecha_creacion;
                                    $datos[] = $viajeNovedades;
                                }

                                return $this->sendRequest(200, "ok", "Datos entregados", [], $datos);
                            }else{

                                return $this->sendRequest(404, "ok", "Sin novedades", [], []);
                            }
                        }else{
                            return $this->sendRequest(404, "ok", "Sin novedades", [], []);
                        }
                        
                    }else{
                        $error = "El nro de viaje no esta asignado a ningun viaje";
                        return $this->sendRequest(400, "error", $error, [$error], []);
                    }
                } catch (\Throwable $th) {
                    $error = $th->getMessage();
                    return $this->sendRequest(500, "error", "Ha ocurrido un error en el servidor al procesar la solicitud", [$error], []);
                }

                
            }
        // fin listado de novedades por viaje
        


        // categoria rendicion
            public function actionCategoriarendicion(){

                $this->cabecerasGET();

                date_default_timezone_set("America/Santiago");
            
                $respuesta = new stdClass();
                if (isset(getallheaders()["Authorization"])) {
                    $token = getallheaders()["Authorization"];
                    $decodeToken = $this->decodeToken($token);
                    if($decodeToken->estado != "ok"){
                        return $this->sendRequest(401, "error", "Token Vencido", ["token vencido"], []);
                    }

                }else{
                    $respuesta->estado = "error";
                    $respuesta->respuesta = "token invalido";
                    $respuesta->mensaje = [];
                    return $respuesta;
                }



                $categoriaRendicion = RendicionCategoria::find()->where(["fecha_borrado" => null])->orderBy(["nombre" => SORT_ASC])->all();

                $categoriaArreglo = [];
                if(count($categoriaRendicion) > 0){
                    
                    foreach ($categoriaRendicion as $key => $value) {
                        $categoriaArreglo[$key]["id"] = $value["id"];
                        $categoriaArreglo[$key]["categoria"] = $value["nombre"];
                    }
                    
                    $respuesta->estado = "ok";
                    $respuesta->respuesta = "datos entregados";
                    $respuesta->mensaje = $categoriaArreglo;
                    

                }else{
                    $respuesta->estado = "ok";
                    $respuesta->respuesta = "No hay categorias de rendicion disponibles.";
                    $respuesta->mensaje = [];
                }
                                            

            
                return $respuesta;
                
            }
        // fin categoria rendicion

        // motivo de rendicion
            public function actionMotivorendicion(){

                $this->cabecerasGET();
                date_default_timezone_set("America/Santiago");
            
                $respuesta = new stdClass();
                if (isset(getallheaders()["Authorization"])) {
                    $token = getallheaders()["Authorization"];
                    $decodeToken = $this->decodeToken($token);
                    if($decodeToken->estado != "ok"){
                        return $this->sendRequest(401, "error", "Token Vencido", ["token vencido"], []);
                    }

                }else{
                    $respuesta->estado = "error";
                    $respuesta->respuesta = "token invalido";
                    $respuesta->mensaje = [];
                    return $respuesta;
                }

                $errores = [];

                if ($_GET) {
                    $_categoriaId = isset($_GET["categoria_id"]) ? $_GET["categoria_id"] : null;
                }else{
                    $respuesta->estado = "error";
                    $respuesta->respuesta = "servicio inaccesible";
                    $respuesta->mensaje = $errores;
                    return $respuesta;
                }


                if (!isset($_categoriaId) || $_categoriaId == "" || $_categoriaId == null) {
                    $errores[] = 'El campo categoria_id es requerido';
                }
    
                if (count($errores) > 0) {
                    $respuesta->estado = "error";
                    $respuesta->respuesta = "detalle errores";
                    $respuesta->mensaje = $errores;
                    return $respuesta;
                }else{



                    $motivoRendicion = RendicionMotivo::find()->where(["rendicion_categoria_id" => $_categoriaId, "fecha_borrado" => null])->orderBy(["nombre" => SORT_ASC])->all();

                    $motivoArreglo = [];
                    if(count($motivoRendicion) > 0){
                        
                        foreach ($motivoRendicion as $key => $value) {
                            $motivoArreglo[$key]["id"] = $value["id"];
                            $motivoArreglo[$key]["motivo"] = $value["nombre"];
                            $motivoArreglo[$key]["categoria"] = $value->rendicionCategoria->nombre;
                        }
                        
                        $respuesta->estado = "ok";
                        $respuesta->respuesta = "datos entregados";
                        $respuesta->mensaje = $motivoArreglo;
                        

                    }else{
                        $respuesta->estado = "ok";
                        $respuesta->respuesta = "No hay motivos de rendicion para la categoria selccionada.";
                        $respuesta->mensaje = [];
                    }
                                                
                    return $respuesta;
                    
                }

            }     
        // fin motivo rendicion

        // tipo de documento
            public function actionTipodocumento(){

                $this->cabecerasGET();
                date_default_timezone_set("America/Santiago");
            
                $respuesta = new stdClass();
                if (isset(getallheaders()["Authorization"])) {
                    $token = getallheaders()["Authorization"];
                    $decodeToken = $this->decodeToken($token);
                    if($decodeToken->estado != "ok"){
                        return $this->sendRequest(401, "error", "Token Vencido", ["token vencido"], []);
                    }

                }else{
                    $respuesta->estado = "error";
                    $respuesta->respuesta = "token invalido";
                    $respuesta->mensaje = [];
                    return $respuesta;
                }

                $errores = [];


                $tipoDocumento = TipoDocumentos::find()->orderBy(["descripcion" => SORT_ASC])->all();

                $tipoArreglo = [];
                if(count($tipoDocumento) > 0){
                    
                    foreach ($tipoDocumento as $key => $value) {
                        $tipoArreglo[$key]["id"] = $value->id;
                        $tipoArreglo[$key]["nro_interno"] = $value->nro_interno;
                        $tipoArreglo[$key]["descripcion"] = $value->descripcion;
                    }
                    
                    $respuesta->estado = "ok";
                    $respuesta->respuesta = "datos entregados";
                    $respuesta->mensaje = $tipoArreglo;
                    

                }else{
                    $respuesta->estado = "ok";
                    $respuesta->respuesta = "No hay tipos de documentos de rendición.";
                    $respuesta->mensaje = [];
                }
                                            
                return $respuesta;
                    


            }   
        // fin tipo de documento


    // /////////////////////////////////////////////////// FIN ACCIONES ////////////////////////////////////////////


    // funciones complementarias
        public function validarFormatoFecha($fecha, $tituloParada){
            
            $respuesta = new stdClass();
            $banderaFechaEntradaOrigen = 0;
            if (isset($fecha)) {
                // se corta el array por el espacio
                $fecha = explode(" ", $fecha);
                //si count no es 2
                if (count($fecha) != 2) {
                    $banderaFechaEntradaOrigen = 1;
                }else{
                    //se corta el array en la primera posicion por guion
                    $fecha2 = explode("-", $fecha[0]);
                    //si no es igual a 3
                    if (count($fecha2) != 3) {
                        $banderaFechaEntradaOrigen = 1;

                    }else{
                        //si año no tiene 4 digitios, mes 2 y dia 2 --- error
                        // echo strlen($fecha2[0]);exit;
                        if (strlen($fecha2[0]) != 4 || strlen($fecha2[1]) != 2 || strlen($fecha2[2]) != 2) {
                            $banderaFechaEntradaOrigen = 1;
                        }
                        //se corta el array en la primera posicion por guion
                        $fecha3 = explode(":", $fecha[1]);
                        //si no es igual a 3
                        if (count($fecha3) != 3) {
                            $banderaFechaEntradaOrigen = 1;
                        }else{
                            //si hora no tiene 2 digitios, minutos 2 y segundos 2 --- error
                            if (strlen($fecha3[0]) != 2 || strlen($fecha3[1]) != 2 || strlen($fecha3[2]) != 2) {
                                $banderaFechaEntradaOrigen = 1;
                            }
                        }
                    }
                }
            }

            if ($banderaFechaEntradaOrigen == 1) {
                $respuesta->estado = "error";
                $respuesta->respuesta = $tituloParada. " no tiene el formato especificado";
                $respuesta->mensaje = [];
                return $respuesta;
            }
        }

        public function validarKey($key){

            $session = Yii::$app->session;
            if (isset($session["keyApi"])) {
                if ($key == $session["keyApi"]) {
                    return 1;
                }else{
                    $keyUsuario = Usuarios::find()->where(["key_api" => $key])->one();

                    if ($keyUsuario) {
                        return 1;
                    }else{
                        return null;
                    }
                }   
            }else{
                
                $keyUsuario = Usuarios::find()->where(["key_api" => $key])->one();

                if ($keyUsuario) {
                    return 1;
                }else{
                    return null;
                }
            }

        }

        public function idUsuarioAPI(){
            $session = Yii::$app->session;
            $key = $session["keyApi"];
            $idUsuario = Usuarios::find()->where(["key_api" => $key])->one();
            return $idUsuario;
        }


        public function insertarLogViajes($viajeId, $datos, $observacion, $viajeDetalleId){

            $idUsuario = $this->idUsuarioAPI();
            $log = new ViajesLog;
            $log->viaje_id = $viajeId;
            $log->estatus_viaje_id = 1;
            $log->usuario_id = 1;
            $log->valores_antiguos = null;
            $log->valores_nuevos = $datos;
            $log->fecha_actualizacion = date("Y-m-d H:i:s");
            $log->observaciones = $observacion;
            $log->tipo_insercion = 1;
            $log->viaje_detalle_id = $viajeDetalleId;
            $log->save();
            // if ($log->save()) {
            //     echo "si";
            //     exit;
            // }else{
            //      echo '<pre>';
            //      var_dump($log->getErrors());
            //      exit;
            // }
        }

        public function insertarAuditoria($permisoID, $descripcion){
            $auditoria = new Auditoria();
            $auditoria->usuario_id = 1;
            $auditoria->permiso_id = $permisoID;
            $auditoria->descripcion = $descripcion;
            $auditoria->fecha_creacion = date("Y-m-d H:i:s");
            $auditoria->save(); 
        }

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

                if(time() > $tokenDecode->exp){
                    $respuesta->estado = "error";
                    $respuesta->respuesta = "token vencido";
                    $respuesta->mensaje = [];
                }else{
                    $respuesta->estado = "ok";
                    $respuesta->respuesta = "token vigente";
                    $respuesta->mensaje = [];
                }
    
                return $respuesta;
                
            } catch (\Throwable $th) {

                $respuesta->estado = "error";
                $respuesta->respuesta = "token expirado";
                $respuesta->mensaje = [];
                return $respuesta;
            }

        }

        public function buscarTipoRampla($id){
            $tipoRampla = TipoRampla::findOne($id);

            if($tipoRampla){
                return $tipoRampla->tipo;
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


    // INTEGRACION CON OS
        public function enviarPodOS($viaje_id){
        
            $viaje = Viajes::findOne($viaje_id);
            $configuracionGlobal = ConfiguracionGlobal::find()->one();
            $datos = new stdClass();
            $datos->token = $configuracionGlobal->token_os;
            $datos->ambiente = $configuracionGlobal->ambiente;
            $datos->id_viaje = $viaje_id;
            $datos->nro_viaje = $viaje->nro_viaje;
            try {
                $curl = curl_init();

                curl_setopt_array($curl, array(
                    CURLOPT_URL => "localhost/integracion-tms-os/web/integracion/". "pod",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => json_encode($datos),
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json'
                    ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);

                $resOS =  json_decode($response, true);

                $info['request'] = $resOS["request"];
                $info['response'] = $resOS["response"];

                if(count($info) > 0){

                    $log = new ViajesLog();
                    $log->viaje_id = $viaje_id;
                    $log->estatus_viaje_id = $viaje->estatus_viaje_id;
                    $log->usuario_id = 1;
                    $log->valores_antiguos = json_encode($info['request']);                
                    $log->valores_nuevos = json_encode($info['request']);
                    $log->request = json_encode($info['request']);
                    $log->response = json_encode($info['response']);
                    $log->observaciones = "Envio de POD a OneSight";
                    $log->tipo_insercion = 0;
                    $log->save();

                    return 1;
                }

                return 1;
            } catch (\Exception $e) {

                $log = new ViajesLog();
                    $log->viaje_id = $viaje_id;
                    $log->estatus_viaje_id = $viaje->estatus_viaje_id;
                    $log->usuario_id = 1;
                    $log->valores_antiguos = json_encode($info['request']);                
                    $log->valores_nuevos = json_encode($info['request']);
                    $log->request = json_encode($info['request']);
                    $log->response = json_encode($info['response']);
                    $log->observaciones = "Envio de POD a OneSight - Error {$e->getMessage()}";
                    $log->tipo_insercion = 0;
                    
                    $log->save();
                
                return 0;
            }
                

        }
    // FIN INTEGRACION CON OS



}
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

                        $token = $this->getToken();

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
                                "token_exp" => $token["exp"]
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
    //fin login desde app movil

    //valida patente y subdominio ingresados manualmente o por QR
    public function actionValidarpatentesubdominio(){

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
        
                    $_patente = isset($data->patente) ? $data->patente : null;
                    $_subdominio = isset($data->subdominio) ? $data->subdominio : null;
                
                }

                $errores = [];

                if (!isset($_patente) || $_patente =="" || $_patente == null) {
                    $errores[] = 'El usuario es requerido';
                }
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

                // validar vehiculo
                    $patente = Vehiculos::find()->where(["patente" => strtoupper($_patente)])->andWhere(["fecha_borrado" => null])->one();
    
                    if (!$patente) {
                        $error = "No existe esta patente asociada a ningún vehículo";
                        return $this->sendRequest(404, "error", $error, [$error], []);
                    }else{
                        $patenteId = $patente->id;
                    }
                
                // fin validar vehiculo
                $data = [
                    "patente" =>  $patente->patente,
                    "muestra" =>  $patente->muestra,
                ];
                return $this->sendRequest(200, "ok", "Patente y subdominios válidos", [], $data);

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
        //creacion viajes
            public function actionCrearviaje(){

                $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImtpZCI6Ii1LSTNROW5OUjdiUm9meG1lWm9YcWJIWkdldyJ9.eyJhdWQiOiIwMmM5NTMzMS0zZDFkLTQxM2UtYjdhZC1kYzUzMTRmNGFmNTkiLCJpc3MiOiJodHRwczovL2xvZ2luLm1pY3Jvc29mdG9ubGluZS5jb20vNjIwYTg5MGUtNjBjZi00ZjJkLWE0YTAtZDUyOWY3MjUzNTkxL3YyLjAiLCJpYXQiOjE2NzE1NjUzNDgsIm5iZiI6MTY3MTU2NTM0OCwiZXhwIjoxNjcxNTY5MjQ4LCJhaW8iOiJBVFFBeS84VEFBQUF3c3M0QWdKNm9YcFBIblMvcE9ET2R6Vy9lQkNvdnBJSUU0a09WSWEzNGNvWVFSQlI5T2k4bktzNTNDVEpJYUdLIiwibm9uY2UiOiJmdWxsdHJ1Y2tfZHljX3RtcyIsInJoIjoiMC5BUVlBRG9rS1lzOWdMVS1rb05VcDl5VTFrVEZUeVFJZFBUNUJ0NjNjVXhUMHIxa0dBRE0uIiwic3ViIjoiMUlVUXZYNWk2MXdPbWY3UjVRRG1aNGRXY29tWUFYTnpYcFFCamtJSDFrOCIsInRpZCI6IjYyMGE4OTBlLTYwY2YtNGYyZC1hNGEwLWQ1MjlmNzI1MzU5MSIsInV0aSI6ImJrM0RMdVpQVGtPbjZiZnFFb3UxQUEiLCJ2ZXIiOiIyLjAifQ.DlfsPu2RgDuJjIvSGOBUVfRKkN3x0vjpaQCxCXLajaHgFtX2UZBeITgh0Cf4vGtF_XbOG7xASUQZwsVFMOuiE_IxV5OHR_RrZ63jR5lv_aFxJBV75LAgBe1t2taLp9eYuaEXy4-aqtdO4Zg-3kZ75nVYjEoID_K6iAJ_At0ULqoikXIJHNxhkGUKegix7OGw34iRmnaHiMtQV4lM0MoZOMEBRuQI9ImgvoUQrKbkfEsnuzIA7DqEsBTC4E6btnxvEWCCwITzqPD-qAD7EdPDcJazkwsSws3sr9_U6Z8RxKB4pevWoFauCTbe217bTWWUpLxZhvWuoQMmtI3B8Xg20g";

                $asd = $this->decodeToken($token); 

                echo "<pre>";
                var_dump($asd);
                exit;

                    
                date_default_timezone_set("America/Santiago");
                $key = $this->validarKey(getallheaders()["Autorizacion"]);
            
            
                $respuesta = new stdClass();
                if ($key != null) {
                    
                    if ($_POST) {

                        $_nroViaje = isset($_POST["nro_viaje"]) ? $_POST["nro_viaje"] : null ;
                        $_tipoOperacion = isset($_POST["tipo_operacion"]) ? $_POST["tipo_operacion"] : null ;
                        $_tipoServicio = isset($_POST["tipo_servicio"]) ? $_POST["tipo_servicio"] : null;
                        $_rut  = isset($_POST["rut"]) ? $_POST["rut"] : null;
                        $_cliente  = isset($_POST["cliente"]) ? $_POST["cliente"] : null;
                        $_tipoCargaNombre  = isset($_POST["tipo_carga_nombre"]) ? $_POST["tipo_carga_nombre"] : null;
                        $_tipoCargaCodigo  = isset($_POST["tipo_carga_codigo"]) ? $_POST["tipo_carga_codigo"] : null;
                        
                        $_transportistaRut  = isset($_POST["transportista_rut"]) ? $_POST["transportista_rut"] : null;
                        $_transportistaNombre  = isset($_POST["transportista_nombre"]) ? $_POST["transportista_nombre"] : null;
                        $_conductorUnoRut  = isset($_POST["conductor_uno_rut"]) ? $_POST["conductor_uno_rut"] : null;
                        $_conductorUnoNombre  = isset($_POST["conductor_uno_nombre"]) ? $_POST["conductor_uno_nombre"] : null;
                        $_conductorDosRut = isset($_POST["conductor_dos_rut"]) ? $_POST["conductor_dos_rut"] : null;
                        $_conductorDosNombre  = isset($_POST["conductor_dos_nombre"]) ? $_POST["conductor_dos_nombre"] : null;
                        $_vehiculoUno = isset($_POST["vehiculo_uno"]) ? $_POST["vehiculo_uno"] : null;
                        $_vehiculoDos  = isset($_POST["vehiculo_dos"]) ? $_POST["vehiculo_dos"] : null;
                        

                        $_poligonoOrigen  = isset($_POST["poligono_origen"]) ? $_POST["poligono_origen"] : null ;
                        $_comunaOrigen  = isset($_POST["comuna_origen"]) ? $_POST["comuna_origen"] : null ;
                        
                        
                        $_fechaEntradaOrigen  = isset($_POST["fecha_entrada_origen"]) ? $_POST["fecha_entrada_origen"] : null ;
                        $_fechaSalidaOrigen = isset($_POST["fecha_salida_origen"]) ? $_POST["fecha_salida_origen"] : null ;
                        $_destinoId  = isset($_POST["destino_id"]) ? $_POST["destino_id"] : null;
                        $_poligonoDestino  = isset($_POST["poligono_destino"]) ? $_POST["poligono_destino"] : null ;
                        $_comunaDestino  = isset($_POST["comuna_destino"]) ? $_POST["comuna_destino"] : null ;

                        $_fechaEntradaDestino  = isset($_POST["fecha_entrada_destino"]) ? $_POST["fecha_entrada_destino"] : null;
                        $_fechaSalidaDestino = isset($_POST["fecha_salida_destino"]) ? $_POST["fecha_salida_destino"] :  null;

                        $_rutFacturador  = isset($_POST["rut_facturador"]) ? $_POST["rut_facturador"] : null;
                        $_clienteFacturador  = isset($_POST["cliente_facturador"]) ? $_POST["cliente_facturador"] : null;

                        $_unidadNegocio = isset($_POST["unidad_negocio"]) ? $_POST["unidad_negocio"] : null;
            
            
                    }else{
                        $post = file_get_contents('php://input');
                        $data = json_decode($post);
            
                        $_nroViaje = isset($data->nro_viaje) ? $data->nro_viaje : null;
                        $_tipoOperacion = isset($data->tipo_operacion) ? $data->tipo_operacion : null;
                        $_tipoServicio = isset($data->tipo_servicio) ? $data->tipo_servicio : null;
                        $_rut  = isset($data->rut) ? $data->rut : null;
                        $_cliente  = isset($data->cliente) ? $data->cliente : null;
                        $_tipoCarga  = isset($data->tipo_carga) ? $data->tipo_carga : null;
                        $_tipoCargaNombre  = isset($data->tipo_carga_nombre) ? $data->tipo_carga_nombre : null;
                        $_tipoCargaCodigo  = isset($data->tipo_carga_codigo) ? $data->tipo_carga_codigo : null;

                        $_transportistaRut  = isset($data->transportista_rut) ? $data->transportista_rut : null;
                        $_transportistaNombre  = isset($data->transportista_nombre) ? $data->transportista_nombre : null;
                        $_conductorUnoRut  = isset($data->conductor_uno_rut) ? $data->conductor_uno_rut : null;
                        $_conductorUnoNombre  = isset($data->conductor_uno_nombre) ? $data->conductor_uno_nombre : null;
                        $_conductorDosRut = isset($data->conductor_dos_rut) ? $data->conductor_dos_rut : null;
                        $_conductorDosNombre  = isset($data->conductor_dos_nombre) ? $data->conductor_dos_nombre : null;
                        $_vehiculoUno = isset($data->vehiculo_uno) ? $data->vehiculo_uno : null;
                        $_vehiculoDos  = isset($data->vehiculo_dos) ? $data->vehiculo_dos : null;

                        $_poligonoOrigen  = isset($data->poligono_origen) ? $data->poligono_origen : null;
                        $_comunaOrigen  = isset($data->comuna_origen) ? $data->comuna_origen : null;


                        $_fechaEntradaOrigen  = isset($data->fecha_entrada_origen) ? $data->fecha_entrada_origen : null;
                        $_fechaSalidaOrigen = isset($data->fecha_salida_origen) ? $data->fecha_salida_origen : null;
                        $_destinoId  = isset($data->destino_id) ? $data->destino_id : null;
                        $_poligonoDestino  = isset($data->poligono_destino) ? $data->poligono_destino : null;
                        $_comunaDestino  =isset($data->comuna_destino) ? $data->comuna_destino : null;
                        
                        $_fechaEntradaDestino  = isset($data->fecha_entrada_destino) ? $data->fecha_entrada_destino : null;
                        $_fechaSalidaDestino = isset($data->fecha_salida_destino) ? $data->fecha_salida_destino : null;


                        $_rutFacturador  = isset($data->rut_facturador) ? $data->rut_facturador : null ;
                        $_clienteFacturador  = isset($data->cliente_facturador) ? $data->cliente_facturador : null ;

                        $_unidadNegocio = isset($data->unidad_negocio) ? $data->unidad_negocio : null ;
                    }

            
            
                //validaciones de requeridos
                    // $requeridos = ['_tipoServicioId', '_clienteId'];
                    // $requeridos = ['tipo_servicio_id','cliente_id','origen_id','fecha_entrada_origen','fecha_salida_origen','destino_id','fecha_entrada_destino','fecha_salida_destino'];


                    $errores = [];
                    if (!isset($_nroViaje) || $_nroViaje =="" || $_nroViaje == null) {
                        $errores[] = 'El campo nro_viaje es requerido';
                    }
                    if (!isset($_tipoOperacion) || $_tipoOperacion =="" || $_tipoOperacion == null) {
                        $errores[] = 'El campo tipo_operacion es requerido';
                    }
                    if (!isset($_tipoServicio) || $_tipoServicio =="" || $_tipoServicio == null) {
                        $errores[] = 'El campo tipo_servicio es requerido';
                    }
                    if (!isset($_rut) || $_rut =="" || $_rut == null) {
                        $errores[] = 'El campo rut es requerido';
                    }else{
                        $rutExploide = explode("-", $_rut);
                        if (strlen($rutExploide[0]) < 7) {
                            $errores[] = 'El rut del cliente es invalido, debe ser con guión';
                        }
                    }
                    if (!isset($_rutFacturador) || $_rutFacturador =="" || $_rutFacturador == null) {
                        $errores[] = 'El campo rut_facturador es requerido';
                    }else{
                        $rutExploide = explode("-", $_rutFacturador);
                        if (strlen($rutExploide[0]) < 7) {
                            $errores[] = 'El rut_facturador del cliente_facturador es invalido, debe ser con guión';
                        }
                    }
                    if (!isset($_cliente) || $_cliente =="" || $_cliente == null) {
                        $errores[] = 'El campo cliente es requerido';
                    }
                    if (!isset($_clienteFacturador) || $_clienteFacturador =="" || $_clienteFacturador == null) {
                        $errores[] = 'El campo cliente_facturador es requerido';
                    }
                    if (!isset($_poligonoOrigen) || $_poligonoOrigen =="" || $_poligonoOrigen == null) {
                        $errores[] = 'El campo poligono_destino es requerido';
                    }
                    // if (!isset($_comunaOrigen) || $_comunaOrigen =="" || $_comunaOrigen == null) {
                    //     $errores[] = 'El campo comuna_origen es requerido';
                    // }
                    if (!isset($_poligonoDestino) || $_poligonoDestino =="" || $_poligonoDestino == null) {
                        $errores[] = 'El campo poligono_destino es requerido';
                    }
                    // if (!isset($_comunaDestino) || $_comunaDestino =="" || $_comunaDestino == null) {
                    //     $errores[] = 'El campo comuna_destino es requerido';
                    // }
                    if (!isset($_unidadNegocio) || $_unidadNegocio =="" || $_unidadNegocio == null) {
                        $errores[] = 'El campo unidad_negocio es requerido';
                    }

                    if (!isset($_tipoCargaNombre) || $_tipoCargaNombre =="" || $_tipoCargaNombre == null) {
                        $errores[] = 'El campo tipo_carga_nombre es requerido';
                    }
                    if (!isset($_tipoCargaCodigo) || $_tipoCargaCodigo =="" || $_tipoCargaCodigo == null) {
                        $errores[] = 'El campo tipo_carga_codigo es requerido';
                    }
                    
                    if (!isset($_transportistaRut) || $_transportistaRut == "" || $_transportistaRut == null) {
                        $errores[] = 'El campo transportista_rut es requerido';
                    }else{
                        $rutExploide = explode("-", $_transportistaRut);
                        if (strlen($rutExploide[0]) < 7) {
                            $errores[] = 'El rut del transportista es invalido, debe ser con guión';
                        }
                    }
                    
                    if (!isset($_transportistaNombre  ) || $_transportistaNombre   =="" || $_transportistaNombre   == null) {
                        $errores[] = 'El campo transportista_nombre es requerido';
                    }
                    
                    if (!isset($_conductorUnoRut) || $_conductorUnoRut =="" || $_conductorUnoRut == null) {
                        $errores[] = 'El campo conductor_uno_rut es requerido';
                    }else{
                        $rutExploide = explode("-", $_conductorUnoRut);
                        if (strlen($rutExploide[0]) < 7) {
                            $errores[] = 'El rut del conductor uno es invalido, debe ser con guión';
                        }
                    }
                    
                    if (!isset($_conductorUnoNombre  ) || $_conductorUnoNombre   =="" || $_conductorUnoNombre   == null) {
                        $errores[] = 'El campo conductor_uno_nombre es requerido';
                    }else{
                        $conductorExpl = explode(" ", $_conductorUnoNombre);
                        if (count($conductorExpl) != 2) {
                            $errores[] = 'Debe ingresar un nombre y un apellido para el conductor uno';
                        }
                    }

                    if (!isset($_conductorDosRut) || $_conductorDosRut =="" || $_conductorDosRut == null) {
                        $errores[] = 'El campo conductor_dos_rut es requerido';
                    }else{
                        $rutExploide = explode("-", $_conductorDosRut);
                        if (strlen($rutExploide[0]) < 7) {
                            $errores[] = 'El rut del conductor dos es invalido, debe ser con guión';
                        }
                    }
                    
                    if (!isset($_conductorDosNombre  ) || $_conductorDosNombre   =="" || $_conductorDosNombre   == null) {
                        $errores[] = 'El campo conductor_dos_nombre es requerido';
                    }else{
                        $conductorDosExpl = explode(" ", $_conductorDosNombre);
                        if (count($conductorDosExpl) != 2) {
                            $errores[] = 'Debe ingresar un nombre y un apellido para el conductor dos';
                        }
                    }

                    if (!isset($_vehiculoUno  ) || $_vehiculoUno   == "" || $_vehiculoUno   == null) {
                        $errores[] = 'El campo vehiculo_uno es requerido';
                    }

                    if (!isset($_vehiculoDos  ) || $_vehiculoDos   == "" || $_vehiculoDos   == null) {
                        $errores[] = 'El campo vehiculo_dos es requerido';
                    }
            
                    if (count($errores) > 0) {
                        $respuesta->estado = "error";
                        $respuesta->respuesta = "detalle errores";
                        $respuesta->mensaje = $errores;

                        return $respuesta;
                    }
            
                //fin validaciones de requeridos
            
                    // if ($_POST) {
                        // validar nro de viaje

                            if ($_nroViaje != "") {
                                $nroViaje = Viajes::find()->where(["nro_viaje" => $_nroViaje])->one();
                                if ($nroViaje) {
                                    $respuesta->estado = "error";
                                    $respuesta->respuesta = "Nro. de viaje ya existe para otro viaje";
                                    $respuesta->mensaje = [];

                                    return $respuesta;
                                }
                            }

                        // fin validar nro de viaje

                        // validar tipo de operacion
                            $operacionID = 0;
                            $servicioId = 0;


                            $tipoOperacion = TipoOperacion::find()->where(["upper(nombre)" => strtoupper($_tipoOperacion), "fecha_borrado" => null])->one();

                            if (!$tipoOperacion) {
                                $operacion = new TipoOperacion();
                                $operacion->nombre = strtoupper($_tipoOperacion);
                                $operacion->fecha_creacion = date("Y-m-d H:i:s");

                                if($operacion->save()){

                                    $operacionID = $operacion->id;

                                    // validar tipo de servicio
                                        $tipoServicio = TipoServicio::find()->where(["upper(nombre)" => strtoupper($_tipoServicio), "tipo_operacion_id" => $operacionID, "fecha_borrado" => null])->one();
                                        if (!$tipoServicio) {
                                            $servicio = new TipoServicio();
                                            $servicio->tipo_operacion_id = $operacionID;
                                            $servicio->nombre = strtoupper($_tipoServicio);

                                            if($servicio->save()){
                                                $servicioId = $servicio->id;
                                            }else{
                                                $respuesta->estado = "error";
                                                $respuesta->respuesta = "Error inesperado creando el tipo de servicio";
                                                $respuesta->mensaje = [];
                                                return $respuesta;

                                            }
                                        }else{
                                            $servicioId = $tipoServicio->id;
                                        }
                                    // fin validar tipo de servicio

                                }else{
                                    $respuesta->estado = "error";
                                    $respuesta->respuesta = "Error inesperado creando el tipo de operación";
                                    $respuesta->mensaje = [];
                                    return $respuesta;
                                }

                                // return $respuesta;
                            }else{
                                $operacionID = $tipoOperacion->id;

                                // validar tipo de servicio
                                $tipoServicio = TipoServicio::find()->where(["UPPER(nombre)" => strtoupper($_tipoServicio), "tipo_operacion_id" => $operacionID, "fecha_borrado" => null])->one();
                                if (!$tipoServicio) {
                                    $servicio = new TipoServicio();
                                    $servicio->tipo_operacion_id = $operacionID;
                                    $servicio->nombre = strtoupper($_tipoServicio);

                                    if($servicio->save()){
                                        $servicioId = $servicio->id;
                                    }else{
                                        $respuesta->estado = "error";
                                        $respuesta->respuesta = "Error inesperado creando el tipo de servicio";
                                        $respuesta->mensaje = [];
                                        return $respuesta;
                                    }
                                }else{
                                    $servicioId = $tipoServicio->id;
                                }
                            // fin validar tipo de servicio
                            }

                        // fin validar tipo de operacion
            
                        // validar cliente

                            $cliente = Clientes::find()->where(["rut" => $_rut, "fecha_borrado" => null])->one();
                            $clienteId = 0;
                            if (!$cliente) {
                                $nuevoCliente = new Clientes();
                                $nuevoCliente->rut = $_rut;
                                $nuevoCliente->nombre = $_cliente;
                                $nuevoCliente->nombre_fantasia = $_cliente;
                                $nuevoCliente->comuna_id = 1;
                                $nuevoCliente->region_id = 1;
                                $nuevoCliente->ciudad_id = 1;
                                $nuevoCliente->tipo_cliente_id = 1;
                                if($nuevoCliente->save()){
                                    $clienteId = $nuevoCliente->id;
                                }else{
                                    $respuesta->estado = "error";
                                    $respuesta->respuesta = "Error inesperado al crear el cliente.";
                                    $respuesta->mensaje = [];
                                    return $respuesta;
                                }
                            }else{

                                $clienteId = $cliente->id;
                            }
                        // fin validar cliente

                        // validar cliente facturador

                            $clienteFacturador = ClienteFacturador::find()->where(["rut" => $_rutFacturador, "fecha_borrado" => null])->one();
                            $clienteFacturadorId = 0;
                            if (!$clienteFacturador) {
                                $nuevoClienteFacturador = new ClienteFacturador();
                                $nuevoClienteFacturador->rut = $_rutFacturador;
                                $nuevoClienteFacturador->nombre = $_clienteFacturador;
                                $nuevoClienteFacturador->nombre_fantasia = $_clienteFacturador;
                                $nuevoClienteFacturador->comuna_id = 1;
                                $nuevoClienteFacturador->region_id = 1;
                                $nuevoClienteFacturador->ciudad_id = 1;
                                if($nuevoClienteFacturador->save()){
                                    $clienteFacturadorId = $nuevoClienteFacturador->id;

                                    $this->insertarAuditoria(43, "Creación de cliente facturador desde API");
                                }else{
                                    $respuesta->estado = "error";
                                    $respuesta->respuesta = "Error inesperado al crear el cliente facturador.";
                                    $respuesta->mensaje = [];
                                    return $respuesta;
                                }
                            }else{

                                $clienteFacturadorId = $clienteFacturador->id;
                            }
                        // fin validar cliente facturador
            
                        // validar origen
                            $direccionOrigen = Zonas::find()->where(["id" => strtoupper($_poligonoOrigen), "fecha_borrado" => null])->one();
                            if (!$direccionOrigen) {
                                $respuesta->estado = "error";
                                $respuesta->respuesta = "El poligono de origen no esta asociada a ninguna zona de TMS";
                                $respuesta->mensaje = [];
                                return $respuesta;
                            }else{
                                // $cliente_direccion_origen_id = $direccionOrigen->id;
                                $zonaOrigen = $direccionOrigen->id;
                            }
                            
                        // fin validar origen
    
                        // validar fecha hora entrada origen
            
                            if (isset($_fechaEntradaOrigen)) {
                                if ($_fechaEntradaOrigen != "" || $_fechaEntradaOrigen != null) {
                                    $validarFechaHoraEntradaOrigen = $this->validarFormatoFecha($_fechaEntradaOrigen, "Fecha hora entrada origen");
                                    if ($validarFechaHoraEntradaOrigen != null) {
                                        if ($validarFechaHoraEntradaOrigen->estado == "error") {
                                            return $validarFechaHoraEntradaOrigen;
                                        }
                                    }
                                    $banderaFechaEntradaOrigen = 0;
                                }
                            }
                        // fin validar fecha hora entrada origen
            
                        // validar fecha hora salida origen
                            if (isset($_fechaSalidaOrigen)) {
                                if ($_fechaSalidaOrigen != "" || $_fechaSalidaOrigen != null) {
                                    $validarFechaHoraSalidaOrigen = $this->validarFormatoFecha($_fechaSalidaOrigen, "Fecha hora salida origen");
                                    if ($validarFechaHoraSalidaOrigen != null) {
                                        if ($validarFechaHoraSalidaOrigen->estado == "error") {
                                            return $validarFechaHoraSalidaOrigen;
                                        }
                                    }
                                }   
                            }
                        //fin validar fecha hora salida origen
            
                        //validar hora de entrada menor a hora de salida en origen
                            if (isset($_fechaEntradaOrigen) && isset($_fechaSalidaOrigen)) {
                                # code...
                                if (strtotime($_fechaEntradaOrigen) > strtotime($_fechaSalidaOrigen)) {
                                    $respuesta->estado = "error";
                                    $respuesta->respuesta = "Le fecha de entrada en el origen no puede ser mayor a la fecha de salida";
                                    $respuesta->mensaje = [];

                                    return $respuesta;
                                }
                            }
                        //fin validar hora de entrada menor a hora de salida en origen
            
            
                        // validar destino

                            $direccionDestino = Zonas::find()->where(["id" => $_poligonoDestino, "fecha_borrado" => null])->one();
                            if (!$direccionDestino) {

                                $respuesta->estado = "error";
                                $respuesta->respuesta = "La direccion de destino no esta asociada a ninguna zona de TMS";
                                $respuesta->mensaje = [];
                                return $respuesta;
                            }else{
                                // $cliente_direccion_destino_id = $direccionDestino->id;
                                $zonaDestino = $direccionDestino->id;
                            }
            
                        //fin validar destino
            
                        // validar origen y destino distintos
                            // if ($_destinoId == $_origenId) {
                            //     $respuesta->estado = "error";
                            //     $respuesta->mensaje = "Origen y destino no pueden ser iguales";
                            //     return $respuesta;
                            // }
                        // fin validar origen y destino distintos
            
                        // validar fecha hora entrada origen
                            if (isset($_fechaEntradaDestino)) {
                                if ($_fechaEntradaDestino != "" || $_fechaEntradaDestino != null) {
                                    $validarFechaHoraEntradaDestino = $this->validarFormatoFecha($_fechaEntradaDestino, "Fecha hora entrada destino");
                                    if ($validarFechaHoraEntradaDestino != null) {
                                        if ($validarFechaHoraEntradaDestino->estado == "error") {
                                            return $validarFechaHoraEntradaDestino;
                                        }
                                    }
                                }   
                            }
                        // fin validar fecha hora entrada origen
            
                        // validar fecha hora salida origen
                            if (isset($_fechaSalidaDestino)) {
                                if ($_fechaSalidaDestino != "" || $_fechaSalidaDestino != null) {
                                    $validarFechaHoraEntradaDestino = $this->validarFormatoFecha($_fechaSalidaDestino, "Fecha hora salida destino");
                                    if ($validarFechaHoraEntradaDestino != null) {
                                        if ($validarFechaHoraEntradaDestino->estado == "error") {
                                            return $validarFechaHoraEntradaDestino;
                                        }
                                    }
                                }
                            }
                        //fin validar fecha hora salida origen
            
                        //validar hora de entrada menor a hora de salida en origen
                            if (isset($_fechaEntradaDestino) && isset($_fechaSalidaDestino)) {
                                if (strtotime($_fechaEntradaDestino) > strtotime($_fechaSalidaDestino)) {
                                    $respuesta->estado = "error";
                                    $respuesta->respuesta = "Le fecha de entrada en el destino no puede ser mayor a la fecha de salida";
                                    $respuesta->mensaje = [];
                                    return $respuesta;
                                }
                            }
                        //fin validar hora de entrada menor a hora de salida en origen
            
            
                        //validar hora de salida origen mayor a fecha entrada en destino
                            if (isset($_fechaSalidaOrigen) && isset($_fechaEntradaDestino)) {
                                if (strtotime($_fechaSalidaOrigen) > strtotime($_fechaEntradaDestino)) {

                                    $respuesta->estado = "error";
                                    $respuesta->respuesta = "Le fecha de salida en el origen no puede ser mayor a la fecha de entrada en el destino";
                                    $respuesta->mensaje = [];
                                    return $respuesta;
                                }
                            }
                        //fin validar hora de salida origen mayor a fecha entrada en destino


                        // validar tipo de viaje
                            if (isset($_tipoViaje) && $_tipoViaje != null) {
                                // $_tipoViaje = $this->reemplazarAcentos($_tipoViaje);
                                $tipoViaje = TipoViajes::find()->where(["tipo" => strtoupper($_tipoViaje), "fecha_borrado" => null])->one();
    
                                if (!$tipoViaje) {
                                    $tipoV = new TipoViajes();
                                    $tipoV->tipo = strtoupper($_tipoViaje);
                                    $tipoV->tipo_medio_transporte = "NORMAL";
                                    $tipoV->fecha_creacion = date("Y-m-d H:i:s");

                                    if($tipoV->save()){
                                        $_tipoViaje = $tipoV->id;
                                    }else{
                                        $respuesta->estado = "error";
                                        $respuesta->respuesta = "Error inesperado creando el tipo de viaje";
                                        $respuesta->mensaje = [];
                                        return $respuesta;
                                    }
            
                                    // return $respuesta;
                                }else{
                                    $_tipoViaje = $tipoViaje->id;
                                }
                            }else{
                                $_tipoViaje = 1;
                            }

                        // fin validar tipo de viaje

                        // validar unidad de negocio

                            $unidadNegocio = UnidadNegocio::find()->where(["upper(nombre)" => strtoupper($_unidadNegocio), "fecha_borrado" => null])->one();
                            $unidadNegocioId = 0;
                            if (!$unidadNegocio) {
                                $uNegocio = new UnidadNegocio();
                                $uNegocio->nombre = $_clienteFacturador;
                                $uNegocio->fecha_creacion = date("Y-m-d H:i:s");
                                if($uNegocio->save()){
                                    $unidadNegocioId = $uNegocio->id;

                                    // $this->insertarAuditoria(43, "Creación de cliente facturador desde API");
                                }else{
                                    $respuesta->estado = "error";
                                    $respuesta->respuesta = "Error inesperado al crear la unidad de negocio.";
                                    $respuesta->mensaje = [];
                                    return $respuesta;
                                }
                            }else{

                                $unidadNegocioId = $unidadNegocio->id;
                            }
                            
                        // fin validar unidad de negocio

                        // validar tipo de carga
                            $tipoCarga = TipoCarga::find()->where(["codigo" => $_tipoCargaCodigo, "fecha_borrado" => null])->one();
                            $tipoCargaId = 0;
                            if (!$tipoCarga) {
                                $tCarga = new TipoCarga();
                                $tCarga->tipo = $_tipoCargaNombre;
                                $tCarga->codigo = $_tipoCargaCodigo;
                                $tCarga->fecha_creacion = date("Y-m-d H:i:s");
                                if($tCarga->save()){
                                    $tipoCargaId = $tCarga->id;

                                    // $this->insertarAuditoria(43, "Creación de cliente facturador desde API");
                                }else{
    
                                    $respuesta->estado = "error";
                                    $respuesta->respuesta = "Error inesperado al crear el tipo de carga.";
                                    $respuesta->mensaje = [];
                                    return $respuesta;
                                }
                            }else{

                                $tipoCargaId = $tipoCarga->id;
                            }
                            
                        // fin validar tipo de cargo


                        // validar transportista
                            $transportista = Transportistas::find()->where(["documento" => $_transportistaRut, "fecha_borrado" => null])->one();
                            $transportistaId = 0;
                            if (!$transportista) {
                                $trans = new Transportistas();
                                $trans->documento = $_transportistaRut;
                                $trans->nombre = $_transportistaNombre;
                                $trans->razon_social = $_transportistaNombre;
                                $trans->comuna_id =  1;
                                $trans->region_id =  1;
                                $trans->ciudad_id =  1;
                                $trans->tipo_transportista_id =  1;
                                $trans->estado =  1;
                                $trans->fecha_creacion = date("Y-m-d H:i:s");
                                if($trans->save()){
                                    $transportistaId = $trans->id;

                                    // $this->insertarAuditoria(43, "Creación de cliente facturador desde API");
                                }else{
                                    $respuesta->estado = "error";
                                    $respuesta->respuesta = "Error inesperado al crear el transportista.";
                                    $respuesta->mensaje = [];
                                    return $respuesta;
                                }
                            }else{

                                $transportistaId = $transportista->id;
                            }
                        // fin validar transportista
                        
                        // validar conductores
                            $conductor1 = Conductores::find()->where(["documento" => $_conductorUnoRut, "transportista_id" =>   $transportistaId, "fecha_borrado" => null])->one();
                            $conductorUnoId = 0;
                            if (!$conductor1) {
                                $conductorUno = new Conductores();

                                $conductorUno->transportista_id = $transportistaId;
                                
                                $usuario = explode(" ", $_conductorUnoNombre);

                                $conductorUno->documento = $_conductorUnoRut;
                                $conductorUno->nombre = $usuario[0];
                                $conductorUno->apellido = $usuario[0];
                                $conductorUno->estado_conductor =  1;

                                $conductorUno->usuario = $usuario[0];
                                $clave = explode("-",$_conductorUnoRut);
                                $conductorUno->clave = $clave[0];

                                $conductorUno->fecha_creacion = date("Y-m-d H:i:s");
                                if($conductorUno->save()){
                                    $conductorUnoId = $conductorUno->id;

                                    // $this->insertarAuditoria(43, "Creación de cliente facturador desde API");
                                }else{

                                    $respuesta->estado = "error";
                                    $respuesta->respuesta = "Error inesperado al crear el conductor 1.";
                                    $respuesta->mensaje = [];
                                    return $respuesta;
                                }
                            }else{

                                $conductorUnoId = $conductor1->id;
                            }
                        // fin validar conductores
                        
                        // validar conductores
                            $conductor2 = Conductores::find()->where(["documento" => $_conductorDosRut, "transportista_id" =>   $transportistaId, "fecha_borrado" => null])->one();
                            $conductorDosId = 0;
                            if (!$conductor2) {
                                $conductorDos = new Conductores();

                                $conductorDos->transportista_id = $transportistaId;
                                
                                $usuario = explode(" ", $_conductorDosNombre);

                                $conductorDos->documento = $_conductorDosRut;
                                $conductorDos->nombre = $usuario[0];
                                $conductorDos->apellido = $usuario[0];
                                $conductorDos->estado_conductor =  1;

                                $conductorDos->usuario = $usuario[0];
                                $clave = explode("-",$_conductorDosRut);
                                $conductorDos->clave = $clave[0];

                                $conductorDos->fecha_creacion = date("Y-m-d H:i:s");
                                if($conductorDos->save()){
                                    $conductorDosId = $conductorDos->id;

                                    // $this->insertarAuditoria(43, "Creación de cliente facturador desde API");
                                }else{
                                    $respuesta->estado = "error";
                                    $respuesta->respuesta = "Error inesperado al crear el conductor 1.";
                                    $respuesta->mensaje = [];
                                    return $respuesta;
                                }
                            }else{

                                $conductorDosId = $conductor2->id;
                            }
                        // fin validar conductores

                        // validar vehiculo uno

                            $vehiculo1 = Vehiculos::find()->where(["upper(patente)" => strtoupper($_vehiculoUno), "transportista_id" =>   $transportistaId, "fecha_borrado" => null])->one();
                        
                            $vehiculoUnoId = 0;
                            if (!$vehiculo1) {

                                $respuesta->estado = "error";
                                $respuesta->respuesta = "El vehículo uno no existe en TMS";
                                $respuesta->mensaje = [];
                                return $respuesta;
                            }else{
                                $vehiculoUnoId = $vehiculo1->id;
                            }
                        // fin validar vehiculo uno

                        // validar vehiculo dos
                            $vehiculo2 = Vehiculos::find()->where(["upper(patente)" => strtoupper($_vehiculoDos), "transportista_id" =>   $transportistaId, "fecha_borrado" => null])->one();
                            $vehiculoDosId = 0;
                            if (!$vehiculo2) {

                                $respuesta->estado = "error";
                                $respuesta->respuesta = "El vehiculo dos no existe en TMS";
                                $respuesta->mensaje = [];
                                return $respuesta;
                            }else{
                                $vehiculoDosId = $vehiculo2->id;
                            }
                        // fin validar vehiculo dos
            
                        $viaje = new Viajes();
                        $viaje->nro_viaje = isset($_nroViaje) ? $_nroViaje : null;
                        $viaje->tipo_servicio_id = $servicioId;
                        $viaje->cliente_id =  $clienteId;
                        $viaje->estatus_viaje_id =  2;
                        
                        

                        $viaje->transportista_id = $transportistaId;
                        $viaje->conductor_id =  $conductorUnoId;
                        $viaje->conductor_dos_id =  $conductorDosId;
                        
                        $viaje->vehiculo_uno_id =  $vehiculoUnoId;
                        $viaje->vehiculo_dos_id =  $vehiculoDosId;

                        $viaje->activar_ruta_segura = 0;
                        $viaje->tipo_viaje_id = 1;
                        $viaje->cliente_facturador_id = $clienteFacturadorId;
                        // $viaje->uso_chasis_id = $usoChasisId;
                        $viaje->unidad_negocio_id = $unidadNegocioId;
                        $viaje->tipo_carga_id = $tipoCargaId;
                        // $viaje->observacion = isset($_observacion) ? $_observacion : null;

                        $fecha = date("Y-m-d H:i:s");
            
                        if ($viaje->save()) {
                            $bandera = 0;
                            $viajeNro = Viajes::findOne($viaje->id);
                            if ($viajeNro->nro_viaje == "" || $viajeNro->nro_viaje == null) {
                                $viajeNro->nro_viaje = strval($viaje->id);
                                $viajeNro->save();
                            }
            
                            //origen
                            $viajeDetalleOrigen = new ViajeDetalle();
                            $viajeDetalleOrigen->viaje_id = $viaje->id;
            
                            $viajeDetalleOrigen->zona_id =  $zonaOrigen;
                            // $viajeDetalleOrigen->cliente_direccion_id = $cliente_direccion_origen_id;
                            $viajeDetalleOrigen->orden = 1;
                            $viajeDetalleOrigen->fecha_entrada = isset($_fechaEntradaOrigen) ? $_fechaEntradaOrigen : null;
                            $viajeDetalleOrigen->fecha_salida = isset($_fechaSalidaOrigen) ? $_fechaSalidaOrigen : null;
                            $viajeDetalleOrigen->estado = 0;
                            $viajeDetalleOrigen->semaforo_id = 0;
                            $viajeDetalleOrigen->cambio_estado_manual = 0;
                            // $viajeDetalleOrigen->macrozona_id = $macrozonaOrigenId;
                            // $viajeDetalleOrigen->m_zona_id = $m_zonaOrigenId;
            
                            $viajeDetalleOrigen->fecha_creado = date("Y-m-d H:i:s");
                            if ($viajeDetalleOrigen->save()){
                                // $fechaViaje = explode(" ", $fecha);
                                $viaje->fecha = $fecha;
                                $viaje->fecha_presentacion = $viajeDetalleOrigen->fecha_entrada;
                                $viaje->update();
                            }else{
                                // echo '<pre>';
                                // var_dump($viajeDetalleOrigen->getErrors());
                                // exit;
                                $bandera = 1;
                                
                            }
                            
            
                            //destino
                            $viajeDetalleDestino = new ViajeDetalle();
                            $viajeDetalleDestino->viaje_id = $viaje->id;
            
                            $viajeDetalleDestino->zona_id = $zonaDestino;
                            
                            // $viajeDetalleDestino->cliente_direccion_id = $cliente_direccion_destino_id;
            
                            $viajeDetalleDestino->orden = 2;
                            $viajeDetalleDestino->fecha_entrada = isset($_fechaEntradaDestino) ? $_fechaEntradaDestino : null;
                            $viajeDetalleDestino->fecha_salida = isset($_fechaSalidaDestino) ? $_fechaSalidaDestino : null;
                            $viajeDetalleDestino->estado = 0;
                            $viajeDetalleDestino->fecha_creado = date("Y-m-d H:i:s");
                            $viajeDetalleDestino->semaforo_id = 0;
                            $viajeDetalleDestino->cambio_estado_manual = 0;
                            // $viajeDetalleDestino->macrozona_id = $macrozonaDestinoId;
                            // $viajeDetalleDestino->m_zona_id = $m_zonaDestinoId;
                            if (!$viajeDetalleDestino->save()){
                                $bandera = 1;
                            } else{
                                // $viaje->fecha_presentacion = isset($_fechaEntradaDestino) ? $_fechaEntradaDestino : null;;
                                $viaje->update();
                            }
            

                        }else{
                            $bandera = 1;
                        }
            
                        if ($bandera == 0) {
                            $respuesta->estado = "ok";
                            $respuesta->respuesta = "Viaje agregado con exito: viaje_id = {$viaje->id}";
                            $respuesta->id_viaje = $viaje->nro_viaje;
                            $respuesta->mensaje = [];



                            if ($_POST) {
                                $datos = json_encode($_POST);
                            }else{
                                $datos = json_encode($data);
                            }

                            $this->insertarLogViajes($viaje->id, $datos, "Creado con exito desde API", null);
                            
                            return $respuesta;
                        }else{
                            $respuesta->estado = "error";
                            $respuesta->respuesta = "Error al crear el viaje";
                            $respuesta->mensaje = [];

                            // $this->insertarLogViajes($viaje->id, $datos, "Error desde api");
                            return $respuesta;
                        }
            
            
                    //
                }else{

                    $respuesta->estado = "error";
                    $respuesta->respuesta = "API_KEY invalida";
                    $respuesta->mensaje = [];
                }
            
            
                return $respuesta;
            }
        // fin creacion viajes

        //edicion viajes
            public function actionEditarviaje(){

                    
                date_default_timezone_set("America/Santiago");
                $key = $this->validarKey(getallheaders()["Autorizacion"]);

                $respuesta = new stdClass();
                if ($key != null) {
                    
                    if ($_POST) {
                        $_nroViaje = isset($_POST["nro_viaje"]) ? $_POST["nro_viaje"] : null ;
                        $_tipoOperacion = isset($_POST["tipo_operacion"]) ? $_POST["tipo_operacion"] : null ;
                        $_tipoServicio = isset($_POST["tipo_servicio"]) ? $_POST["tipo_servicio"] : null;
                        $_rut  = isset($_POST["rut"]) ? $_POST["rut"] : null;
                        $_cliente  = isset($_POST["cliente"]) ? $_POST["cliente"] : null;
                        $_tipoCargaNombre  = isset($_POST["tipo_carga_nombre"]) ? $_POST["tipo_carga_nombre"] : null;
                        $_tipoCargaCodigo  = isset($_POST["tipo_carga_codigo"]) ? $_POST["tipo_carga_codigo"] : null;
                        
                        $_transportistaRut  = isset($_POST["transportista_rut"]) ? $_POST["transportista_rut"] : null;
                        $_transportistaNombre  = isset($_POST["transportista_nombre"]) ? $_POST["transportista_nombre"] : null;
                        $_conductorUnoRut  = isset($_POST["conductor_uno_rut"]) ? $_POST["conductor_uno_rut"] : null;
                        $_conductorUnoNombre  = isset($_POST["conductor_uno_nombre"]) ? $_POST["conductor_uno_nombre"] : null;
                        $_conductorDosRut = isset($_POST["conductor_dos_rut"]) ? $_POST["conductor_dos_rut"] : null;
                        $_conductorDosNombre  = isset($_POST["conductor_dos_nombre"]) ? $_POST["conductor_dos_nombre"] : null;
                        $_vehiculoUno = isset($_POST["vehiculo_uno"]) ? $_POST["vehiculo_uno"] : null;
                        $_vehiculoDos  = isset($_POST["vehiculo_dos"]) ? $_POST["vehiculo_dos"] : null;
                        
            
                        $_poligonoOrigen  = isset($_POST["poligono_origen"]) ? $_POST["poligono_origen"] : null ;
                        $_comunaOrigen  = isset($_POST["comuna_origen"]) ? $_POST["comuna_origen"] : null ;
                        
                        
                        $_fechaEntradaOrigen  = isset($_POST["fecha_entrada_origen"]) ? $_POST["fecha_entrada_origen"] : null ;
                        $_fechaSalidaOrigen = isset($_POST["fecha_salida_origen"]) ? $_POST["fecha_salida_origen"] : null ;
                        $_destinoId  = isset($_POST["destino_id"]) ? $_POST["destino_id"] : null;
                        $_poligonoDestino  = isset($_POST["direccion_destino_id"]) ? $_POST["direccion_destino_id"] : null ;
                        $_comunaDestino  = isset($_POST["comuna_destino"]) ? $_POST["comuna_destino"] : null ;
            
                        $_fechaEntradaDestino  = isset($_POST["fecha_entrada_destino"]) ? $_POST["fecha_entrada_destino"] : null;
                        $_fechaSalidaDestino = isset($_POST["fecha_salida_destino"]) ? $_POST["fecha_salida_destino"] :  null;
            
                        $_rutFacturador  = isset($_POST["rut_facturador"]) ? $_POST["rut_facturador"] : null;
                        $_clienteFacturador  = isset($_POST["cliente_facturador"]) ? $_POST["cliente_facturador"] : null;
            
                        $_unidadNegocio = isset($_POST["unidad_negocio"]) ? $_POST["unidad_negocio"] : null;
            
            
                    }else{
                        $post = file_get_contents('php://input');
                        $data = json_decode($post);
            
                        $_nroViaje = isset($data->nro_viaje) ? $data->nro_viaje : null;
                        $_tipoOperacion = isset($data->tipo_operacion) ? $data->tipo_operacion : null;
                        $_tipoServicio = isset($data->tipo_servicio) ? $data->tipo_servicio : null;
                        $_rut  = isset($data->rut) ? $data->rut : null;
                        $_cliente  = isset($data->cliente) ? $data->cliente : null;
                        $_tipoCarga  = isset($data->tipo_carga) ? $data->tipo_carga : null;
                        $_tipoCargaNombre  = isset($data->tipo_carga_nombre) ? $data->tipo_carga_nombre : null;
                        $_tipoCargaCodigo  = isset($data->tipo_carga_codigo) ? $data->tipo_carga_codigo : null;
            
                        $_transportistaRut  = isset($data->transportista_rut) ? $data->transportista_rut : null;
                        $_transportistaNombre  = isset($data->transportista_nombre) ? $data->transportista_nombre : null;
                        $_conductorUnoRut  = isset($data->conductor_uno_rut) ? $data->conductor_uno_rut : null;
                        $_conductorUnoNombre  = isset($data->conductor_uno_nombre) ? $data->conductor_uno_nombre : null;
                        $_conductorDosRut = isset($data->conductor_dos_rut) ? $data->conductor_dos_rut : null;
                        $_conductorDosNombre  = isset($data->conductor_dos_nombre) ? $data->conductor_dos_nombre : null;
                        $_vehiculoUno = isset($data->vehiculo_uno) ? $data->vehiculo_uno : null;
                        $_vehiculoDos  = isset($data->vehiculo_dos) ? $data->vehiculo_dos : null;
            
                        $_poligonoOrigen  = isset($data->poligono_origen) ? $data->poligono_origen : null;
                        $_comunaOrigen  = isset($data->comuna_origen) ? $data->comuna_origen : null;
            
            
                        $_fechaEntradaOrigen  = isset($data->fecha_entrada_origen) ? $data->fecha_entrada_origen : null;
                        $_fechaSalidaOrigen = isset($data->fecha_salida_origen) ? $data->fecha_salida_origen : null;
                        $_destinoId  = isset($data->destino_id) ? $data->destino_id : null;
                        $_poligonoDestino  = isset($data->poligono_destino) ? $data->poligono_destino : null;
                        $_comunaDestino  =isset($data->comuna_destino) ? $data->comuna_destino : null;
                        
                        $_fechaEntradaDestino  = isset($data->fecha_entrada_destino) ? $data->fecha_entrada_destino : null;
                        $_fechaSalidaDestino = isset($data->fecha_salida_destino) ? $data->fecha_salida_destino : null;
            
            
                        $_rutFacturador  = isset($data->rut_facturador) ? $data->rut_facturador : null ;
                        $_clienteFacturador  = isset($data->cliente_facturador) ? $data->cliente_facturador : null ;
            
                        $_unidadNegocio = isset($data->unidad_negocio) ? $data->unidad_negocio : null ;
                    }
            
                //validaciones de requeridos

                    $errores = [];
                    if (!isset($_nroViaje) || $_nroViaje =="" || $_nroViaje == null) {
                        $errores[] = 'El campo nro_viaje es requerido';
                    }

                    if (!isset($_tipoOperacion) && $_tipoServicio != "") {
                        $errores[] = 'Se intenta editar el tipo servicio, pero no se ha especificado el tipo de operación';
                    }

                    if (!isset($_rut) && $_cliente != "") {
                        $errores[] = 'Se intenta editar el cliente, pero no se ha especificado el rut del mismo';
                    }
                    if (!isset($_cliente) && $_rut != "") {
                        $errores[] = 'Se intenta editar el cliente, pero no se ha especificado el nombre del mismo';
                    }
                    
                    if (!isset($_rutFacturador) && $_clienteFacturador != "") {
                        $errores[] = 'Se intenta editar el cliente facturador, pero no se ha especificado el rut del mismo';
                    }

                    if (!isset($_clienteFacturador) && $_rutFacturador != "") {
                        $errores[] = 'Se intenta editar el cliente facturador, pero no se ha especificado el nombre del mismo';
                    }
                    
                    if (isset($_transportistaNombre) && $_transportistaRut == "") {
                        $errores[] = 'Se intenta editar el tranportista, pero no se ha especificado el rut del mismo';
                    }
                    
                    if (isset($_transportistaRut) && $_transportistaNombre == "") {
                        $errores[] = 'Se intenta editar o hacer busquedas por el tranportista para editar conductores o vehículos, pero no se ha especificado el nombre del mismo';
                    }
                    
                    if (!isset($_conductorUnoNombre) && $_conductorUnoRut != "") {
                        $errores[] = 'Se intenta editar el conductor uno, pero no se ha especificado el rut del mismo';
                    }
                    
                    if (!isset($_conductorUnoRut) && $_conductorUnoNombre != "") {
                        $errores[] = 'Se intenta editar el conductor uno, pero no se ha especificado el nombre del mismo';
                    }
                    
                    if (!isset($_conductorUnoNombre) && $_conductorUnoRut != "") {
                        $errores[] = 'Se intenta editar el conductor uno, pero no se ha especificado el rut del mismo';
                    }
                    
                    if (!isset($_conductorUnoRut) && $_conductorUnoNombre != "") {
                        $errores[] = 'Se intenta editar el conductor uno, pero no se ha especificado el nombre del mismo';
                    }

                    if (!isset($_conductorUnoRut) && $_conductorUnoNombre != "" && $_transportistaRut == "") {
                        $errores[] = 'Se intenta editar el conductor uno, pero no se ha especificado el rut del transportista';
                    }
                    
                    if (!isset($_conductorDosNombre) && $_conductorDosRut != "") {
                        $errores[] = 'Se intenta editar el conductor dos, pero no se ha especificado el rut del mismo';
                    }
                    
                    if (!isset($_conductorDosRut) && $_conductorDosNombre != "") {
                        $errores[] = 'Se intenta editar el conductor dos, pero no se ha especificado el nombre del mismo';
                    }

                    if (!isset($_conductorDosRut) && $_conductorDosNombre != "" && $_transportistaRut == "") {
                        $errores[] = 'Se intenta editar el conductor dos, pero no se ha especificado el rut del transportista';
                    }
                    
                    if (isset($_vehiculoUno) && $_transportistaRut == "") {
                        $errores[] = 'Se intenta editar el vehículo uno, pero no se ha especificado el rut del transportista';
                    }
                    
                    if (isset($_vehiculoDos) &&  $_transportistaRut == "") {
                        $errores[] = 'Se intenta editar el vehículo dos, pero no se ha especificado el rut del transportista';
                    }
            
                    if (count($errores) > 0) {

                        $respuesta->estado = "error";
                        $respuesta->respuesta = "detalle errores";
                        $respuesta->mensaje = $errores;
                        return $respuesta;
                    }
                    
                //fin validaciones de requeridos
            
                    // if ($_POST) {
                        // validar nro de viaje
                            $viajeID = 0;
                            $clienteId = 0;
                            if ($_nroViaje != "") {
                                $nroViaje = Viajes::find()->where(["nro_viaje" => $_nroViaje])->one();
                                if (!$nroViaje) {
                                    $respuesta->estado = "error";
                                    $respuesta->respuesta = "Nro de viaje no está asociado a ningún viaje";
                                    $respuesta->mensaje = [];
                                    return $respuesta;
                                }else{
                                    $viajeID = $nroViaje->id;
                                    $clienteId = $nroViaje->cliente_id;
                                }
                            }

                        // fin validar nro de viaje

                        // validar tipo de operacion
                            $operacionID = 0;
                            $servicioId = 0;
                            if (isset($_tipoOperacion)) {

                                $tipoOperacion = TipoOperacion::find()->where(["UPPER(nombre)" => strtoupper($_tipoOperacion)])->one();

                                if (!$tipoOperacion) {
                                    $operacion = new TipoOperacion();
                                    $operacion->nombre = strtoupper($_tipoOperacion);
                                    $operacion->fecha_creacion = date("Y-m-d H:i:s");

                                    if($operacion->save()){

                                        $operacionID = $operacion->id;

                                        // validar tipo de servicio
                                            $tipoServicio = TipoServicio::find()->where(["UPPER(nombre)" => strtoupper($_tipoServicio), "tipo_operacion_id" => $operacionID])->one();
                                            if (!$tipoServicio) {
                                                $servicio = new TipoServicio();
                                                $servicio->tipo_operacion_id = $operacionID;
                                                $servicio->nombre = strtoupper($_tipoServicio);

                                                if($servicio->save()){
                                                    $servicioId = $servicio->id;
                                                }else{

                                                    $respuesta->estado = "error";
                                                    $respuesta->respuesta = "Error inesperado creando el tipo de servicio";
                                                    $respuesta->mensaje = [];
                                                    return $respuesta;
                                                }
                                            }else{
                                                $servicioId = $tipoServicio->id;
                                            }
                                        // fin validar tipo de servicio


                                    }else{
                                        $respuesta->estado = "error";
                                        $respuesta->respuesta = "Error inesperado creando el tipo de operación";
                                        $respuesta->mensaje = [];
                                        return $respuesta;
                                    }

                                    // return $respuesta;
                                }else{
                                    $operacionID = $tipoOperacion->id;

                                    // validar tipo de servicio
                                    $tipoServicio = TipoServicio::find()->where(["UPPER(nombre)" => strtoupper($_tipoServicio), "tipo_operacion_id" => $operacionID])->one();
                                    if (!$tipoServicio) {
                                        $servicio = new TipoServicio();
                                        $servicio->tipo_operacion_id = $operacionID;
                                        $servicio->nombre = strtoupper($_tipoServicio);

                                        if($servicio->save()){
                                            $servicioId = $servicio->id;
                                        }else{
                                            $respuesta->estado = "error";
                                            $respuesta->respuesta = "Error inesperado creando el tipo de servicio";
                                            $respuesta->mensaje = [];
                                            return $respuesta;
                                        }
                                    }else{
                                        $servicioId = $tipoServicio->id;
                                    }
                                // fin validar tipo de servicio
                                }
                            }

                        // fin validar tipo de operacion
            
                        // validar cliente
                            $clienteId = 0;
                            if (isset($_rut)) {
                                $cliente = Clientes::find()->where(["rut" => $_rut])->one();
                                if (!$cliente) {
                                    $nuevoCliente = new Clientes();
                                    $nuevoCliente->rut = $_rut;
                                    $nuevoCliente->nombre = $_cliente;
                                    $nuevoCliente->nombre_fantasia = $_cliente;
                                    $nuevoCliente->comuna_id = 1;
                                    $nuevoCliente->region_id = 1;
                                    $nuevoCliente->ciudad_id = 1;
                                    $nuevoCliente->tipo_cliente_id = 1;
                                    if($nuevoCliente->save()){
                                        $clienteId = $nuevoCliente->id;
                                    }else{

                                        $respuesta->estado = "error";
                                        $respuesta->respuesta = "Error inesperado al crear el cliente.";
                                        $respuesta->mensaje = [];
                                        return $respuesta;
                                    }
                                }else{

                                    $clienteId = $cliente->id;
                                }
                            }

                        // fin validar cliente

                        // validar cliente facturador
                            $clienteFacturadorId = 0;
                            if (isset($_rutFacturador)) {
                                $clienteFacturador = ClienteFacturador::find()->where(["rut" => $_rutFacturador])->one();
                                if (!$clienteFacturador) {
                                    $nuevoClienteFacturador = new ClienteFacturador();
                                    $nuevoClienteFacturador->rut = $_rutFacturador;
                                    $nuevoClienteFacturador->nombre = $_clienteFacturador;
                                    $nuevoClienteFacturador->nombre_fantasia = $_clienteFacturador;
                                    $nuevoClienteFacturador->comuna_id = 1;
                                    $nuevoClienteFacturador->region_id = 1;
                                    $nuevoClienteFacturador->ciudad_id = 1;
                                    if($nuevoClienteFacturador->save()){
                                        $clienteFacturadorId = $nuevoClienteFacturador->id;

                                        $this->insertarAuditoria(43, "Creación de cliente facturador desde API");
                                    }else{
                                        $respuesta->estado = "error";
                                        $respuesta->respuesta = "Error inesperado al crear el cliente facturador.";
                                        $respuesta->mensaje = [];
                                        return $respuesta;
                                    }
                                }else{

                                    $clienteFacturadorId = $clienteFacturador->id;
                                }
                            }
                        // fin validar cliente facturador
            
                        // validar origen
                            $zonaOrigen = 0;
                            // $cliente_direccion_origen_id = 0;
                            if (isset($_poligonoOrigen)) {
                                $poligonoOrigen = Zonas::find()->where(["id" => $_poligonoOrigen, "fecha_borrado" => null])->one();
                                if (!$poligonoOrigen) {
                                    $respuesta->estado = "error";
                                    $respuesta->respuesta = "El id no esta asociado a ningun poligono de TMS";
                                    $respuesta->mensaje = [];
                                    return $respuesta;
                                }else{
                                    // $cliente_direccion_origen_id = $poligonoOrigen->id;
                                    $zonaOrigen = $poligonoOrigen->id;
                                }
                            }
                            
                        // fin validar origen
            
                        // validar fecha hora entrada origen
            
                            if (isset($_fechaEntradaOrigen)) {
                                if ($_fechaEntradaOrigen != "") {
                                    $validarFechaHoraEntradaOrigen = $this->validarFormatoFecha($_fechaEntradaOrigen, "Fecha hora entrada origen");
                                    if ($validarFechaHoraEntradaOrigen != null) {
                                        if ($validarFechaHoraEntradaOrigen->estado == "error") {
                                            return $validarFechaHoraEntradaOrigen;
                                        }
                                    }
                                    $banderaFechaEntradaOrigen = 0;
                                }
                            }
                        // fin validar fecha hora entrada origen
            
                        // validar fecha hora salida origen
                            if (isset($_fechaSalidaOrigen)) {

                                if ($_fechaSalidaOrigen != "") {
                                    $validarFechaHoraSalidaOrigen = $this->validarFormatoFecha($_fechaSalidaOrigen, "Fecha hora salida origen");
                                    if ($validarFechaHoraSalidaOrigen != null) {
                                        if ($validarFechaHoraSalidaOrigen->estado == "error") {
                                            return $validarFechaHoraSalidaOrigen;
                                        }
                                    }
                                }
                            }
                        //fin validar fecha hora salida origen
            
                        //validar hora de entrada menor a hora de salida en origen
                            if (isset($_fechaEntradaOrigen) && isset($_fechaSalidaOrigen)) {
                                
                                if (strtotime($_fechaEntradaOrigen) > strtotime($_fechaSalidaOrigen)) {
                                    $respuesta->estado = "error";
                                    $respuesta->respuesta = "Le fecha de entrada en el origen no puede ser mayor a la fecha de salida";
                                    $respuesta->mensaje = [];
                                    return $respuesta;
                                }
                            }
                        //fin validar hora de entrada menor a hora de salida en origen
            
                        // validar destino
                            $zonaDestino = 0;
                            // $cliente_direccion_destino_id = 0;
                            
                            if (isset($_poligonoDestino)) {
                                
                                $poligonoDestino = Zonas::find()->where(["id" => $_poligonoDestino, "fecha_borrado" => null])->one();
                                if (!$poligonoDestino) {
                                    $respuesta->estado = "error";
                                    $respuesta->respuesta = "La direccion de destino no esta asociada a ninguna zona de TMS";
                                    $respuesta->mensaje = [];
                                    return $respuesta;
                                }else{
                                    // $cliente_direccion_destino_id = $poligonoDestino->id;
                                    $zonaDestino = $poligonoDestino->id;
                                }
                            }
            
                        //fin validar destino
            
                        // validar fecha hora entrada origen
                            if (isset($_fechaEntradaDestino)) {


                                if ($_fechaEntradaDestino != "") {

                                    $validarFechaHoraEntradaDestino = $this->validarFormatoFecha($_fechaEntradaDestino, "Fecha hora entrada destino");
                                    if ($validarFechaHoraEntradaDestino != null) {
                                        if ($validarFechaHoraEntradaDestino->estado == "error") {
                                            return $validarFechaHoraEntradaDestino;
                                        }
                                    }
                                }
                            }
                        // fin validar fecha hora entrada origen
            
                        // validar fecha hora salida origen
                            if (isset($_fechaSalidaDestino)) {
                                if ($_fechaSalidaDestino != "") {
                                    $validarFechaHoraEntradaDestino = $this->validarFormatoFecha($_fechaSalidaDestino, "Fecha hora salida destino");
                                    if ($validarFechaHoraEntradaDestino != null) {
                                        if ($validarFechaHoraEntradaDestino->estado == "error") {
                                            return $validarFechaHoraEntradaDestino;
                                        }
                                    }
                                }
                            }
                        //fin validar fecha hora salida origen
            
                        //validar hora de entrada menor a hora de salida en origen
                            if (isset($_fechaEntradaDestino) && isset($_fechaSalidaDestino)) {
                                if (strtotime($_fechaEntradaDestino) > strtotime($_fechaSalidaDestino)) {
                                    $respuesta->estado = "error";
                                    $respuesta->respuesta = "Le fecha de entrada en el destino no puede ser mayor a la fecha de salida";
                                    $respuesta->mensaje = [];
                                    return $respuesta;
                                }
                            }
                        //fin validar hora de entrada menor a hora de salida en origen
            
            
                        //validar hora de salida origen mayor a fecha entrada en destino
                            if (isset($_fechaSalidaOrigen) && isset($_fechaEntradaDestino)) {
                                if (strtotime($_fechaSalidaOrigen) > strtotime($_fechaEntradaDestino)) {
                                    $respuesta->estado = "error";
                                    $respuesta->respuesta = "Le fecha de salida en el origen no puede ser mayor a la fecha de entrada en el destino";
                                    $respuesta->mensaje = [];
                                    return $respuesta;
                                }
                            }
                        //fin validar hora de salida origen mayor a fecha entrada en destino


                        // validar tipo de viaje
                            // if ($_tipoViaje != null) {
                            //     // $_tipoViaje = $this->reemplazarAcentos($_tipoViaje);
                            //     $tipoViaje = TipoViajes::find()->where(["tipo" => strtoupper($_tipoViaje)])->one();
    
                            //     if (!$tipoViaje) {
                            //         $tipoV = new TipoViajes();
                            //         $tipoV->tipo = strtoupper($_tipoViaje);
                            //         $tipoV->tipo_medio_transporte = "NORMAL";
                            //         $tipoV->fecha_creacion =  date("Y-m-d H:i:s");

                            //         if($tipoV->save()){
                            //             $_tipoViaje = $tipoV->id;
                            //         }else{
                            //             $respuesta->estado = "error";
                            //             $respuesta->mensaje = "Error inesperado creando el tipo de viaje";
                            //             return $respuesta;
                            //         }
            
                            //         // return $respuesta;
                            //     }else{
                            //         $_tipoViaje = $tipoViaje->id;

                            //     }
                            // }else{
                            //     $_tipoViaje = 1;
                            // }

                        // fin validar tipo de viaje

                        // validar unidad de negocio

                            $unidadNegocioId = 0;
                            if(isset($_unidadNegocio)){

                                $unidadNegocio = UnidadNegocio::find()->where(["upper(nombre)" => strtoupper($_unidadNegocio)])->one();
                                if (!$unidadNegocio) {
                                    $uNegocio = new UnidadNegocio();
                                    $uNegocio->nombre = $_unidadNegocio;
                                    $uNegocio->fecha_creacion = date("Y-m-d H:i:s");
                                    if($uNegocio->save()){
                                        $unidadNegocioId = $uNegocio->id;
        
                                        // $this->insertarUditoria(43, "Creación de cliente facturador desde API");
                                    }else{
                                        $respuesta->estado = "error";
                                        $respuesta->respuesta = "Error inesperado al crear la unidad de negocio.";
                                        $respuesta->mensaje = [];
                                        return $respuesta;
                                    }
                                }else{
        
                                    $unidadNegocioId = $unidadNegocio->id;
                                }
                            }
                            
                        // fin validar unidad de negocio
                            
                        // validar tipo de carga
                            $tipoCargaId = 0;
                            if(isset($_tipoCargaCodigo)){
                                $tipoCarga = TipoCarga::find()->where(["codigo" => $_tipoCargaCodigo, "fecha_borrado" => null])->one();
                                if (!$tipoCarga) {
                                    $tCarga = new TipoCarga();
                                    $tCarga->tipo = $_tipoCargaNombre;
                                    $tCarga->codigo = $_tipoCargaCodigo;
                                    $tCarga->fecha_creacion = date("Y-m-d H:i:s");
                                    if($tCarga->save()){
                                        $tipoCargaId = $tCarga->id;

                                        $this->insertarAuditoria(43, "Creación de tipo de carga desde API");
                                    }else{
                                        $respuesta->estado = "error";
                                        $respuesta->respuesta = "Error inesperado al crear el tipo de carga.";
                                        $respuesta->mensaje = [];
                                        return $respuesta;
                                    }
                                }else{

                                    $tipoCargaId = $tipoCarga->id;
                                }
                            }
                            
                        // fin validar tipo de cargo

                        // validar transportista
                            if(isset($_transportistaRut) && isset($_transportistaNombre)){
                                $transportista = Transportistas::find()->where(["documento" => $_transportistaRut, "fecha_borrado" => null])->one();
                                $transportistaId = 0;
                                if (!$transportista) {
                                    $trans = new Transportistas();
                                    $trans->documento = $_transportistaRut;
                                    $trans->nombre = $_transportistaNombre;
                                    $trans->razon_social = $_transportistaNombre;
                                    $trans->comuna_id =  1;
                                    $trans->region_id =  1;
                                    $trans->ciudad_id =  1;
                                    $trans->tipo_transportista_id =  1;
                                    $trans->estado =  1;
                                    $trans->fecha_creacion = date("Y-m-d H:i:s");
                                    if($trans->save()){
                                        $transportistaId = $trans->id;

                                        // $this->insertarAuditoria(43, "Creación de cliente facturador desde API");
                                    }else{

                                        $respuesta->estado = "error";
                                        $respuesta->respuesta = "Error inesperado al crear el transportista.";
                                        $respuesta->mensaje = [];
                                        return $respuesta;
                                    }
                                }else{

                                    $transportistaId = $transportista->id;
                                }
                            }
                        // fin validar transportista
                        
                        // validar conductores
                            $conductorUnoId = 0;
                            if(isset($_conductorUnoRut)){

                                $conductor1 = Conductores::find()->where(["documento" => $_conductorUnoRut, "transportista_id" =>   $transportistaId, "fecha_borrado" => null])->one();
                                if (!$conductor1) {
                                    $conductorUno = new Conductores();
        
                                    $conductorUno->transportista_id = $transportistaId;
                                    
                                    $usuario = explode(" ", $_conductorUnoNombre);
        
                                    $conductorUno->documento = $_conductorUnoRut;
                                    $conductorUno->nombre = $usuario[0];
                                    $conductorUno->apellido = $usuario[0];
                                    $conductorUno->estado_conductor =  1;
        
                                    $conductorUno->usuario = $usuario[0];
                                    $clave = explode("-",$_conductorUnoRut);
                                    $conductorUno->clave = $clave[0];
        
                                    $conductorUno->fecha_creacion = date("Y-m-d H:i:s");
                                    if($conductorUno->save()){
                                        $conductorUnoId = $conductorUno->id;
        
                                        // $this->insertarAuditoria(43, "Creación de cliente facturador desde API");
                                    }else{
                            
                                        $respuesta->estado = "error";
                                        $respuesta->respuesta = "Error inesperado al crear el conductor 1.";
                                        $respuesta->mensaje = [];
                                        return $respuesta;
                                    }
                                }else{
        
                                    $conductorUnoId = $conductor1->id;
                                }

                            }
                        // fin validar conductores
                        
                        // validar conductores
                            if(isset($_conductorDosRut)){

                                $conductor2 = Conductores::find()->where(["documento" => $_conductorDosRut, "transportista_id" =>   $transportistaId, "fecha_borrado" => null])->one();
                                $conductorDosId = 0;
                                if (!$conductor2) {
                                    $conductorDos = new Conductores();
        
                                    $conductorDos->transportista_id = $transportistaId;
                                    
                                    $usuario = explode(" ", $_conductorDosNombre);
        
                                    $conductorDos->documento = $_conductorDosRut;
                                    $conductorDos->nombre = $usuario[0];
                                    $conductorDos->apellido = $usuario[0];
                                    $conductorDos->estado_conductor =  1;
        
                                    $conductorDos->usuario = $usuario[0];
                                    $clave = explode("-",$_conductorDosRut);
                                    $conductorDos->clave = $clave[0];
        
                                    $conductorDos->fecha_creacion = date("Y-m-d H:i:s");
                                    if($conductorDos->save()){
                                        $conductorDosId = $conductorDos->id;
        
                                        // $this->insertarAuditoria(43, "Creación de cliente facturador desde API");
                                    }else{
                                        $respuesta->estado = "error";
                                        $respuesta->respuesta = "Error inesperado al crear el conductor 2.";
                                        $respuesta->mensaje = [];
                                        return $respuesta;
                                    }
                                }else{
        
                                    $conductorDosId = $conductor2->id;
                                }
                            }
                        // fin validar conductores

                        // validar vehiculo uno
                        
                            $vehiculoUnoId = 0;
                            if(isset($_vehiculoUno)){
                                $vehiculo1 = Vehiculos::find()->where(["upper(patente)" => strtoupper($_vehiculoUno), "transportista_id" =>   $transportistaId, "fecha_borrado" => null])->one();
                                
                                if (!$vehiculo1) {
                                    $respuesta->estado = "error";
                                    $respuesta->respuesta = "El vehículo uno no existe en TMS o no esta asignado el transportista enviado";
                                    $respuesta->mensaje = [];
                                    return $respuesta;
                                }else{
                                    $vehiculoUnoId = $vehiculo1->id;
                                }
                            }
                        // fin validar vehiculo uno

                        // validar vehiculo dos
                            $vehiculoDosId = 0;
                            if(isset($_vehiculoDos)){
                                $vehiculo2 = Vehiculos::find()->where(["upper(patente)" => strtoupper($_vehiculoDos), "transportista_id" =>   $transportistaId, "fecha_borrado" => null])->one();
                                if (!$vehiculo2) {
                                    $respuesta->estado = "error";
                                    $respuesta->respuesta = "El vehiculo dos no existe en TMS o no esta asignado el transportista enviado";
                                    $respuesta->mensaje = [];
                                    return $respuesta;
                                }else{
                                    $vehiculoDosId = $vehiculo2->id;
                                }
                            }
                        // fin validar vehiculo dos

                        $viaje = Viajes::find()->where(["id" => $viajeID])->one();
                            
                        $viaje->tipo_servicio_id = $servicioId == 0 ? $viaje->tipo_servicio_id : $servicioId ;
                        $viaje->cliente_id =  $clienteId == 0 ? $viaje->cliente_id : $clienteId;

                        $viaje->tipo_viaje_id = 1;

                        $fecha = isset($_fechaEntradaOrigen) ? $_fechaEntradaOrigen : date("Y-m-d 00:00:00");
                        
                        $viaje->cliente_facturador_id = $clienteFacturadorId == 0 ? $viaje->cliente_facturador_id : $clienteFacturadorId;
                        $viaje->uso_chasis_id = 1  ;
                        $viaje->unidad_negocio_id = $unidadNegocioId == 0 ? $viaje->unidad_negocio_id : $unidadNegocioId;
                        $viaje->tipo_carga_id = $tipoCargaId == 0 ? $viaje->tipo_carga_id : $tipoCargaId;
                        $viaje->vehiculo_uno_id = $vehiculoUnoId == 0 ? $viaje->vehiculo_uno_id : $vehiculoUnoId;
                        $viaje->vehiculo_dos_id = $vehiculoDosId == 0 ? $viaje->vehiculo_dos_id : $vehiculoDosId;
                        // $viaje->observacion = isset($_observacion) ? $_observacion : $viaje->observacion;


                        if ($viaje->save()) {
                            $bandera = 0;

                            //origen
                            $viajeDetalleOrigen = ViajeDetalle::find()->where(["viaje_id" => $viajeID, "orden" => 1])->one();
                            $viajeDetalleOrigen->zona_id =  $zonaOrigen == 0 ? $viajeDetalleOrigen->zona_id : $zonaOrigen;
                            // $viajeDetalleOrigen->cliente_direccion_id = $cliente_direccion_origen_id == 0 ? $viajeDetalleOrigen->cliente_direccion_id : $cliente_direccion_origen_id;
                            
                            $viajeDetalleOrigen->fecha_entrada = isset($_fechaEntradaOrigen) ? $_fechaEntradaOrigen : $viajeDetalleOrigen->fecha_entrada;
                            $viajeDetalleOrigen->fecha_salida = isset($_fechaSalidaOrigen) ? $_fechaSalidaOrigen : $viajeDetalleOrigen->fecha_salida;
                            $viajeDetalleOrigen->estado = 0;
                            $viajeDetalleOrigen->semaforo_id = 0;
                            $viajeDetalleOrigen->cambio_estado_manual = 0;
                            // $viajeDetalleOrigen->macrozona_id = $macrozonaOrigenId == 0 ? $viajeDetalleOrigen->macrozona_id : $macrozonaOrigenId;
                            // $viajeDetalleOrigen->m_zona_id = $m_zonaOrigenId == 0 ? $viajeDetalleOrigen->m_zona_id : $m_zonaOrigenId;

                            $viajeDetalleOrigen->fecha_edicion = date("Y-m-d H:i:s");
                            if ($viajeDetalleOrigen->save()){

                                $viaje->fecha_presentacion =  isset($_fechaEntradaOrigen) ? $_fechaEntradaOrigen : $viajeDetalleOrigen->fecha_entrada;
                                $viaje->update();

                            }else{
                                $bandera = 1;  
                            }
                            
                            //destino
                            $viajeDetalleDestino = ViajeDetalle::find()->where(["viaje_id" => $viajeID])->orderBy(['orden' => SORT_DESC])->one();

                            $viajeDetalleDestino->zona_id = $zonaDestino == 0 ? $viajeDetalleDestino->zona_id : $zonaDestino;
                            
                            // $viajeDetalleDestino->cliente_direccion_id = $cliente_direccion_destino_id == 0 ? $viajeDetalleDestino->cliente_direccion_id : $cliente_direccion_destino_id;
                            
                            $viajeDetalleDestino->fecha_entrada = isset($_fechaEntradaDestino) ? $_fechaEntradaDestino : $viajeDetalleDestino->fecha_entrada;
                            $viajeDetalleDestino->fecha_salida = isset($_fechaSalidaDestino) ? $_fechaSalidaDestino : $viajeDetalleDestino->fecha_salida;
                            $viajeDetalleDestino->estado = 0;
                            $viajeDetalleDestino->fecha_edicion = date("Y-m-d H:i:s");
                            $viajeDetalleDestino->semaforo_id = 0;
                            $viajeDetalleDestino->cambio_estado_manual = 0;
                            // $viajeDetalleDestino->macrozona_id = $macrozonaDestinoId == 0 ? $viajeDetalleDestino->macrozona_id : $macrozonaDestinoId;
                            // $viajeDetalleDestino->m_zona_id = $m_zonaDestinoId == 0 ? $viajeDetalleDestino->m_zona_id : $m_zonaDestinoId;

                            if (!$viajeDetalleDestino->save()){
                                
                                $bandera = 1;
                            } else{
                                
                            }
            
                        }else{
                            $bandera = 1;
                        }
            
                        if ($bandera == 0) {

                            $respuesta->estado = "ok";
                            $respuesta->respuesta = "Viaje editado con exito: viaje_id = {$viaje->id}";
                            $respuesta->id_viaje = $viaje->nro_viaje;
                            $respuesta->mensaje = [];

                            if ($_POST) {
                                $datos = json_encode($_POST);
                            }else{
                                $datos = json_encode($data);
                            }

                            $this->insertarLogViajes($viaje->id, $datos, "Edicion de viaje con exito desde API", null);
                            return $respuesta;
                        }else{

                            $respuesta->estado = "error";
                            $respuesta->respuesta = "Error inesperado al editar el viaje";
                            $respuesta->mensaje = [];
                            return $respuesta;
                        }

                }else{
                    $respuesta->estado = "error";
                    $respuesta->respuesta = "API_KEY invalida";
                    $respuesta->mensaje = [];
                }
            
            
                return $respuesta;
            }
        //fin edicion viajes

        //crear paradas a un viaje
            public function actionAnularviaje(){
                date_default_timezone_set("America/Santiago");
            
                $key = $this->validarKey(getallheaders()["Autorizacion"]);

    
                $respuesta = new stdClass();
                if ($key != null) {
            
                    if ($_POST) {
                        $_nroViaje = isset($_POST["nro_viaje"]) ? $_POST["nro_viaje"] : null ;
            
                    }else{
                        $post = file_get_contents('php://input');
                        $data = json_decode($post);
            
                        $_nroViaje = isset($data->nro_viaje) ? $data->nro_viaje : null;
            
                    }
                    
                    //validaciones de requeridos
                        $errores = [];
            
                        if (!isset($_nroViaje) || $_nroViaje =="" || $_nroViaje == null) {
                            $errores[] = 'El campo nro_viaje es requerido';
                        }
            
                        if (count($errores) > 0) {
                            $respuesta->estado = "error";
                            $respuesta->respuesta = "detalle errores";
                            $respuesta->mensaje = $errores;
                            return $respuesta;
                        }
                    //fin validaciones de requeridos
            
                    // if ($_POST) {
                        // validar nro de viaje
                            $viajeID = 0;
                            $nroViaje = Viajes::find()->where(["nro_viaje" => $_nroViaje])->one();
                            if (!$nroViaje) {
                                $respuesta->estado = "error";
                                $respuesta->respuesta = "No existe ningun viaje con este identificador";
                                $respuesta->mensaje = [];
                                return $respuesta;
                            }else{
                                $viajeID = $nroViaje->id;
                            }
                            
                        // fin validar nro de viaje

                    // }

                    $viajeAnular = Viajes::find()->where(["id" => $viajeID])->one();

                    if($viajeAnular){

                        // $viajeAnular->nro_viaje = $viajeAnular->nro_viaje."_".date("Ymdhms"); 
                        $viajeAnular->estatus_viaje_id = 9; 

                        if($viajeAnular->save()){
                            $respuesta->estado = "error";
                            $respuesta->respuesta = "Viaje Anulado con exito: nro_viaje = {$_nroViaje}";
                            $respuesta->mensaje = [];

                            if ($_POST) {
                                $datos = json_encode($_POST);
                            }else{
                                $datos = json_encode($data);
                            }

                            $this->insertarLogViajes($viajeID, $datos, "Anulación de viaje con exito desde API", null);
                            return $respuesta;
                        }else{
                            $respuesta->estado = "error";
                            $respuesta->respuesta = "error interno al anular el viaje";
                            $respuesta->mensaje = [];
                        }
                    }else{
                        $respuesta->estado = "error";
                        $respuesta->respuesta = "No existe el viaje a anular";
                        $respuesta->mensaje = [];
                        return $respuesta;
                    }
                    
                }else{
                    $respuesta->estado = "error";
                    $respuesta->respuesta = "API_KEY invalida";
                    $respuesta->mensaje = [];
                }
            
                return $respuesta;
                
            }
        //fin crear paradas a un viaje


        // listado de viajes por vehiculo
            public function actionListadoviajesvehiculo(){

                try {
                    $this->cabecerasGET();
                    date_default_timezone_set('America/Santiago');
                    $respuesta = new stdClass();
                    
                    if (isset(getallheaders()["Authorization"])) {
                        $token = getallheaders()["Authorization"];
                        $decodeToken = $this->decodeToken($token);
                        if($decodeToken->estado != "ok"){
                            return $decodeToken;
                        }
                    }else{
                        return $this->sendRequest(400, "error", "Token Invalido", ["token invalido"], $data);
                    }
        
                    $respuesta = new stdClass();
                    // if ($key != null) {
                        
                    if ($_GET) {    
                        $error = "Servicio Innacceible";
                        return $this->sendRequest(405, "error", $error, [$error], []);
            
                    }else{
                        $post = file_get_contents('php://input');
                        $data = json_decode($post);
    
                        $_patente = isset($data->patente) ? $data->patente : null;
                        $_conductorId = isset($data->conductor_id) ? $data->conductor_id : null;
                        $_accionOperacion = isset($data->accion_operacion) ? $data->accion_operacion : null;
                        $_subdominio = isset($data->subdominio) ? $data->subdominio : null;
            
                    }
    
                        
                    //validaciones de requeridos
                        $errores = [];
            
                        if (!isset($_patente) || $_patente == "" || $_patente == null) {
                            $errores[] = 'El campo patente es requerido';
                        }
            
                        if (!isset($_conductorId) || $_conductorId == "" || $_conductorId == null) {
                            $errores[] = 'El campo conductor_id es requerido';
                        }
                        if (!isset($_accionOperacion) || $_accionOperacion == "" || $_accionOperacion == null) {
                            $errores[] = 'El campo accion_operacion es requerido';
                        }
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
    
                    // validar vehiculo
                        $patente = Vehiculos::find()->where(["patente" => $_patente])->andWhere(["fecha_borrado" => null])->one();
    
                        
                        if (!$patente) {
                            $error = "No existe esta patente asociada a ningun vehiculo";
                            return $this->sendRequest(400, "error", $error, [$error], []);
                        }else{
                            $patenteId = $patente->id;
                        }
                    
                    // fin validar vehiculo
    
                    // validar accion operacion para el caso
                        if (isset($_accionOperacion) && $_accionOperacion != '') {
        
                            if (intval($_accionOperacion) > 2) {
                                $accionOperacion = 0;
                            }else{
                                $accionOperacion = $_accionOperacion;
                            }       
                        }
                
                    // fin validar accion operacion para el caso
    
    
                    switch ($accionOperacion) {
                        //viajes activos del dia actual
                        case 0:
                            $model = Viajes::find()->where(['vehiculo_uno_id' => $patenteId, "conductor_id" => $_conductorId ])->andWhere(['BETWEEN', 'fecha_presentacion', date("Y-m-d 00:00:00"), date("Y-m-d 23:59:59")])->andWhere(["not in", "estatus_viaje_id", [1,6,9]])->orderBy(["id" => SORT_DESC])->all();   
                            break;
                        //viajes activos del dia de mañana
                        case 1:
                            $model = Viajes::find()->where(['vehiculo_uno_id' => $patenteId, "conductor_id" => $_conductorId ])->andWhere(['BETWEEN', 'fecha_presentacion', date("Y-m-d 00:00:00", strtotime("+1 day")), date("Y-m-d 23:59:59", strtotime("+1 day"))])->andWhere(["not in", "estatus_viaje_id", [1,6,9]])->orderBy(["id" => SORT_DESC])->all();  
                            break; 
                        //viajes completados dia actual
                        case 2:
                            $model = Viajes::find()->where(['vehiculo_uno_id' => $patenteId, "conductor_id" => $_conductorId ])->andWhere(['BETWEEN', 'fecha_presentacion', date("Y-m-d 00:00:00"), date("Y-m-d 23:59:59")])->andWhere(['procesado' => true])->andWhere(["in", "estatus_viaje_id", [6]])->orderBy(["id" => SORT_DESC])->all();  
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
    
                            
                            $viajeDetalle = ViajeDetalle::find()->where(["viaje_id" => $v->id])->orderBy(["orden" => SORT_ASC])->all();
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
                                $mensaje = "No existen viajes asociados para esa patente, para hoy";
                                return $this->sendRequest(404, "ok", $mensaje, [$mensaje], []);
                            break;
                            //viajes activos del dia de mañana
                            case 1:
                                $mensaje = "No existen viajes asociados para esa patente, para mañana";
                                return $this->sendRequest(404, "ok", $mensaje, [$mensaje], []);
                            break; 
                            //viajes completados dia actual
                            case 2:
                                $mensaje = "No existen viajes asociados para esa patente, completados para hoy";
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
                        return $decodeToken;
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
                        return $decodeToken;
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
                        return $decodeToken;
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
                        return $decodeToken;
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

        //crear paradas a un viaje
            public function actionCrearparadas(){
                date_default_timezone_set("America/Santiago");
            
            
                $key = $this->validarKey(getallheaders()["Autorizacion"]);
                            
                $respuesta = new stdClass();
                if ($key != null) {
            
                    if ($_POST) {



                        $_nroViaje = isset($_POST["nro_viaje"]) ? $_POST["nro_viaje"] : null ;
                        $_poligonoParada = isset($_POST["poligono_parada"]) ? $_POST["poligono_parada"] : null ;
                        $_fechaEntrada = isset($_POST["fecha_entrada"]) ? $_POST["fecha_entrada"] : null ;
                        $_fechaSalida = isset($_POST["fecha_salida"]) ? $_POST["fecha_salida"] : null ;
                        $_poligonoParadaAnterior = isset($_POST["poligono_parada_anterior"]) ? $_POST["poligono_parada_anterior"] : null;

                
            
                    }else{
                        $post = file_get_contents('php://input');
                        $data = json_decode($post);
            
                        $_nroViaje = isset($data->nro_viaje) ? $data->nro_viaje : null;
                        $_poligonoParada = isset($data->poligono_parada) ? $data->poligono_parada : null;
                        $_fechaEntrada = isset($data->fecha_entrada) ? $data->fecha_entrada : null;
                        $_fechaSalida = isset($data->fecha_salida) ? $data->fecha_salida : null;
                        $_poligonoParadaAnterior = isset($data->poligono_parada_anterior) ? $data->poligono_parada_anterior : null;
            
                    }
            
                    //validaciones de requeridos
                        // $requeridos = ['viaje_id','poligono_parada_anterior_id'];
                        $errores = [];
                        // foreach ($requeridos as $k => $v) {
                        //     if (!isset($_POST[$v])) {
                        //         $errores[] = 'El campo '.$v.' es requerido';
                        //     }
                        // }
            
                        if (!isset($_nroViaje) || $_nroViaje =="" || $_nroViaje == null) {
                            $errores[] = 'El campo nro_viaje es requerido';
                        }
                        if (!isset($_poligonoParada) || $_poligonoParada =="" || $_poligonoParada == null) {
                            $errores[] = 'El campo poligono_parada es requerido';
                        }
                        if (!isset($_poligonoParadaAnterior) || $_poligonoParadaAnterior =="" || $_poligonoParadaAnterior == null) {
                            $errores[] = 'El campo poligono_parada_anterior es requerido';
                        }
            
                        if (count($errores) > 0) {
                            $respuesta->estado = "error";
                            $respuesta->respuesta = "detalle errores";
                            $respuesta->mensaje = $errores;
                            return $respuesta;
                        }
                    //fin validaciones de requeridos
            
                    // if ($_POST) {
                        // validar nro de viaje
                            $viajeID = 0;
                            $clienteId = 0;
                            $nroViaje = Viajes::find()->where(["nro_viaje" => $_nroViaje])->one();
                            if (!$nroViaje) {
                                $respuesta->estado = "error";
                                $respuesta->respuesta = "No existe ningun viaje con este identificador";
                                $respuesta->mensaje = [];
                                return $respuesta;
                            }else{
                                $clienteId = $nroViaje->cliente_id;
                                $viajeID = $nroViaje->id;
                            }
                            
                        // fin validar nro de viaje

                        // validar direccion parada nueva
                        $zonaParadaId = 0;
                        $direccionParada = Zonas::find()->where(["id" => $_poligonoParada , "fecha_borrado" => null])->one();

                        if (!$direccionParada) {
                                // $nuevaDireccionParada = new ClienteDirecciones();
                                // $nuevaDireccionParada->cliente_id = $clienteId;
                                // $nuevaDireccionParada->zona_id = 65073; //id poligono ficticio fulltruck
                                // $nuevaDireccionParada->direccion = $_poligonoParada;
                                // $nuevaDireccionParada->fecha_creacion = date("Y-m-d H:i:s");
                                // if($nuevaDireccionParada->save()){
                                //     $cliente_poligono_parada_id = $nuevaDireccionParada->id;
                                //     $zonaParadaId = $nuevaDireccionParada->zona_id;
                                // }else{

                                //     $respuesta->estado = "error";
                                //     $respuesta->mensaje = "Error inesperado al crear la nueva dirección de parada.";
                                //     return $respuesta;
                                // }
                                $respuesta->estado = "error";
                                $respuesta->respuesta = "El polígono nuevo no existe en TMS";
                                $respuesta->mensaje = [];
                                return $respuesta;
                            }else{
                                // $cliente_poligono_parada_id = $direccionParada->id;
                                $zonaParadaId = $direccionParada->id;
                            }
                            
                        // fin validar direccion parada nueva
            
                        // validar fecha hora entrada
                            if (isset($_fechaEntrada)) {
                                $validarFechaEntrada = $this->validarFormatoFecha($_fechaEntrada, "Fecha entrada");
                                if ($validarFechaEntrada != null) {
                                    if ($validarFechaEntrada->estado == "error") {
                                        return $validarFechaEntrada;
                                    }
                                }
                            }
                        // fin validar fecha hora entrada
            
                        // validar fecha hora salida
                            if (isset($_fechaSalida)) {
                                $validarFechaSalida = $this->validarFormatoFecha($_fechaSalida, "Fecha salida");
                                if ($validarFechaSalida != null) {
                                    if ($validarFechaSalida->estado == "error") {
                                        return $validarFechaSalida;
                                    }
                                }
                            }
                        // fin validar fecha hora salida
                            
                        // validar direccion de parada anterior

                            $viajeDetalleParadaAnterior = 0;

                            $direccionParadaAnterior = Zonas::find()->where(["id" => $_poligonoParadaAnterior, "fecha_borrado" => null])->one();


                            $viajeDetalle = ViajeDetalle::find()->where(["viaje_id" => $viajeID, "zona_id" => $direccionParadaAnterior->id])->orderBy(['orden'=>SORT_ASC])->one();

                            if (!$viajeDetalle) {
                                $respuesta->estado = "error";
                                $respuesta->respuesta = "El polígono anterior no existe para este viaje.";
                                $respuesta->mensaje = [];
                                return $respuesta;
                            }else{
                                $viajeDetalleParadaAnterior = $viajeDetalle;
                            }
                        // fin validar direccion de parada anterior
                        
            
                    // }

                    $paradasMayoresA = ViajeDetalle::find()->where(["viaje_id" => $viajeID])->andWhere([">","orden", $viajeDetalleParadaAnterior->orden])->all();
                                                
                    foreach ($paradasMayoresA as $kPM => $vPM) {
                        $vPM->orden =  $vPM->orden+1;
                        $vPM->fecha_entrada_gps = null;
                        $vPM->fecha_salida_gps = null;
                        $vPM->estado =  0;
                        $vPM->fecha_edicion = date("Y-m-d H:i:s");
                        $vPM->semaforo_id = 0;
                        $vPM->eta = null;
                        $vPM->update();
                    }
                    
                    $paradaNuevaInsertar = new ViajeDetalle();

                    $paradaNuevaInsertar->viaje_id = $viajeID;
                    $paradaNuevaInsertar->zona_id = $zonaParadaId; 
                    $paradaNuevaInsertar->orden = $viajeDetalleParadaAnterior->orden +1;
                    
                    $paradaNuevaInsertar->fecha_entrada = null;
                    //recalcular todas las paradas que siguen despues de esa, setear el viaje en asignado.
                    if (isset($_fechaEntrada)) {
                        $fechaE = explode(" ", $_fechaEntrada);
                        $fechaEntrada = explode("-", $fechaE[0]);
                        $paradaNuevaInsertar->fecha_entrada = $fechaEntrada[0]."-".$fechaEntrada[1]."-".$fechaEntrada[2]." ".$fechaE[1];
                        
                    }
                    
                    $paradaNuevaInsertar->fecha_salida = null;
                    if (isset($_fechaSalida)) {
                        $fechaS = explode(" ", $_fechaSalida);
                        $fechaSalida = explode("-", $fechaS[0]);
                        $paradaNuevaInsertar->fecha_salida = $fechaSalida[0]."-".$fechaSalida[1]."-".$fechaSalida[2]." ".$fechaS[1];
                    }


                    // $paradaNuevaInsertar->cliente_direccion_id = $cliente_poligono_parada_id;
                    $paradaNuevaInsertar->estado = 0;
                    $paradaNuevaInsertar->fecha_creado = date("Y-m-d H:i:s");
                    $paradaNuevaInsertar->semaforo_id = 0;
                    $paradaNuevaInsertar->cambio_estado_manual = 0;


                    if($paradaNuevaInsertar->save()){
                        
                        if ($_POST) {
                            $datos = json_encode($_POST);
                        }else{
                            $datos = json_encode($data);
                        }

                        $this->insertarLogViajes($viajeID, $datos, "Parada con exito creada desde API", $paradaNuevaInsertar->id);

                        $respuesta->estado = "ok";
                        $respuesta->respuesta = "Parada agregada con exito: nro_viaje = {$_nroViaje}";
                        $respuesta->mensaje = [];
                        return $respuesta;
                    }else{

                        // echo '<pre>';
                        // var_dump($paradaNuevaInsertar->getErrors());
                        // exit;
                        if ($_POST) {
                            $datos = json_encode($_POST);
                        }else{
                            $datos = json_encode($data);
                        }

                        $this->insertarLogViajes($viajeID, $datos, "Error creando parada desde api", null);

                        $respuesta->estado = "error";
                        $respuesta->respuesta = "Error inesperado al crear la parada";
                        $respuesta->mensaje = [];
                        return $respuesta;
                    }

                    // se busca la parada anterior a donde se quiere guardar la nueva parada
                    // $viajeDetalleParadaAnterior = null;
            
                    // if($_poligonoParadaAnteriorId != ""){
                    //     $viajeDetalleParadaAnterior = ViajeDetalle::find()->where(["viaje_id" => $_viajeId, "id" => $_poligonoParadaAnteriorId])->one();
                        
                    // }
            
                    
                }else{
                    $respuesta->estado = "error";
                    $respuesta->respuesta = "API_KEY invalida";
                    $respuesta->mensaje = [];
                }
            
                return $respuesta;
                
            }
        //fin crear paradas a un viaje

        //Editar paradas a un viaje
        public function actionEditarparada(){
            date_default_timezone_set("America/Santiago");
        
            $key = $this->validarKey(getallheaders()["Autorizacion"]);

            $respuesta = new stdClass();
            if ($key != null) {
        
                if ($_POST) {

                    $_nroViaje = isset($_POST["nro_viaje"]) ? $_POST["nro_viaje"] : null ;
                    $_poligonoParada = isset($_POST["poligono_parada"]) ? $_POST["poligono_parada"] : null ;
                    $_poligonoParadaNueva = isset($_POST["poligono_parada_nueva"]) ? $_POST["poligono_parada_nueva"] : null;
                    $_fechaEntrada = isset($_POST["fecha_entrada"]) ? $_POST["fecha_entrada"] : null ;
                    $_fechaSalida = isset($_POST["fecha_salida"]) ? $_POST["fecha_salida"] : null ;
        
                }else{
                    $post = file_get_contents('php://input');
                    $data = json_decode($post);
        
                    $_nroViaje = isset($data->nro_viaje) ? $data->nro_viaje : null;
                    $_poligonoParada = isset($data->poligono_parada) ? $data->poligono_parada : null;
                    $_poligonoParadaNueva = isset($data->poligono_parada_nueva) ? $data->poligono_parada_nueva : null;
                    $_fechaEntrada = isset($data->fecha_entrada) ? $data->fecha_entrada : null;
                    $_fechaSalida = isset($data->fecha_salida) ? $data->fecha_salida : null;
                }
        
                //validaciones de requeridos
                    // $requeridos = ['viaje_id','parada_anterior_id'];
                    $errores = [];
                    // foreach ($requeridos as $k => $v) {
                    //     if (!isset($_POST[$v])) {
                    //         $errores[] = 'El campo '.$v.' es requerido';
                    //     }
                    // }
        
                    if (!isset($_nroViaje) || $_nroViaje =="" || $_nroViaje == null) {
                        $errores[] = 'El campo nro_viaje es requerido';
                    }
                    if (!isset($_poligonoParada) || $_poligonoParada =="" || $_poligonoParada == null) {
                        $errores[] = 'El campo poligono_parada es requerido';
                    }
                    if (!isset($_poligonoParadaNueva) || $_poligonoParadaNueva =="" || $_poligonoParadaNueva == null) {
                        $errores[] = 'El campo poligono_parada_nueva es requerido';
                    }
        
                    if (count($errores) > 0) {
                        $respuesta->estado = "error";
                        $respuesta->respuesta = "detalle errores";
                        $respuesta->mensaje = $errores;
                        return $respuesta;
                    }
                //fin validaciones de requeridos
        
                // if ($_POST) {
                    // validar nro de viaje
                        $viajeID = 0;
                        $clienteId = 0;
                        $nroViaje = Viajes::find()->where(["nro_viaje" => $_nroViaje])->one();
                        if (!$nroViaje) {
                            $respuesta->estado = "error";
                            $respuesta->respuesta = "No existe ningun viaje con este identificador";
                            $respuesta->mensaje = [];
                            return $respuesta;
                        }else{
                            $clienteId = $nroViaje->cliente_id;
                            $viajeID = $nroViaje->id;
                        }
                        
                    // fin validar nro de viaje

                    // validar direccion parada nueva
                        $zonaParadaId = 0;
                        $poligonoParada = Zonas::find()->where(["id" => $_poligonoParadaNueva, "fecha_borrado" => null])->one();
                        if (!$poligonoParada) {
                            $respuesta->estado = "error";
                            $respuesta->respuesta = "El id no está asociado a ningun poligono en TMS.";
                            $respuesta->mensaje = [];
                            return $respuesta;
                        }else{
                            $zonaParadaId = $poligonoParada->id;
                        }
                        
                    // fin validar direccion parada nueva
        
                    // validar fecha hora entrada
                        if (isset($_fechaEntrada)) {
                            $validarFechaEntrada = $this->validarFormatoFecha($_fechaEntrada, "Fecha entrada");
                            if ($validarFechaEntrada != null) {
                                if ($validarFechaEntrada->estado == "error") {
                                    return $validarFechaEntrada;
                                }
                            }
                        }
                    // fin validar fecha hora entrada
        
                    // validar fecha hora salida
                        if (isset($_fechaSalida)) {
                            $validarFechaSalida = $this->validarFormatoFecha($_fechaSalida, "Fecha salida");
                            if ($validarFechaSalida != null) {
                                if ($validarFechaSalida->estado == "error") {
                                    return $validarFechaSalida;
                                }
                            }
                        }
                    // fin validar fecha hora salida
                        
                    // validar direccion de parada 

                        $viajeDetalleParada = 0;

                        $poligonoParadaAnterior = Zonas::find()->where(["id" => $_poligonoParada, "fecha_borrado" => null])->one();

                        $viajeDetalle = ViajeDetalle::find()->where(["viaje_id" => $viajeID, "zona_id" => $poligonoParadaAnterior->id])->one();


                        if (!$viajeDetalle) {
                            $respuesta->estado = "error";
                            $respuesta->respuesta = "El poligono a editar no existe para este viaje.";
                            $respuesta->mensaje = [];
                            return $respuesta;
                        }else{
                            $viajeDetalleParada = $viajeDetalle;
                        }
                    // fin validar direccion de parada 
                    
            
                // }

                $paradaAeditar = ViajeDetalle::find()->where(["viaje_id" => $viajeID, "id" => $viajeDetalleParada->id])->one();
                                        

                $paradaAeditar->zona_id = $zonaParadaId; 
                
                //recalcular todas las paradas que siguen despues de esa, setear el viaje en asignado.
                if (isset($_fechaEntrada)) {
                    $paradaAeditar->fecha_entrada = null;
                    $fechaE = explode(" ", $_fechaEntrada);
                    $fechaEntrada = explode("-", $fechaE[0]);
                    $paradaAeditar->fecha_entrada = $fechaEntrada[2]."-".$fechaEntrada[1]."-".$fechaEntrada[0]." ".$fechaE[1];
                }
                
                if (isset($_fechaSalida)) {
                    $fechaS = explode(" ", $_fechaSalida);
                    $paradaAeditar->fecha_salida = null;
                    $fechaSalida = explode("-", $fechaS[0]);
                    $paradaAeditar->fecha_salida = $fechaSalida[2]."-".$fechaSalida[1]."-".$fechaSalida[0]." ".$fechaS[1];
                }


                // $paradaAeditar->cliente_direccion_id = $cliente_direccion_parada_id;
                $paradaAeditar->estado = 0;
                $paradaAeditar->fecha_edicion = date("Y-m-d H:i:s");
                $paradaAeditar->semaforo_id = 0;

                if($paradaAeditar->save()){
                    
                    $respuesta->estado = "ok";
                    $respuesta->respuesta = "Parada editada con exito: nro_viaje = {$_nroViaje}";
                    $respuesta->mensaje = [];
                    return $respuesta;
                }else{
                    $respuesta->estado = "error";
                    $respuesta->respuesta = "Error inesperado al insertar la parada ";
                    $respuesta->mensaje = [];
                    return $respuesta;
                }
        
                
            }else{
                $respuesta->estado = "error";
                $respuesta->respuesta = "API_KEY invalida";
                $respuesta->mensaje = [];
            }
        
            return $respuesta;
            
        }
        //fin Editar paradas a un viaje


        // eliminar todas las paradas de un viaje
            public function actionEliminarparada(){
                date_default_timezone_set("America/Santiago");
            
                $key = $this->validarKey(getallheaders()["Autorizacion"]);

                $respuesta = new stdClass();
                if ($key != null) {
            
                    if ($_POST) {

                        $_viajeId = isset($_POST["viaje_id"]) ? $_POST["viaje_id"] : null ;
                        $_zonaId = isset($_POST["zona_id"]) ? $_POST["zona_id"] : null ;
                
            
                    }else{
                        $post = file_get_contents('php://input');
                        $data = json_decode($post);
            
                        $_viajeId = isset($data->viaje_id) ? $data->viaje_id : null;
                        $_zonaId = isset($data->zona_id) ? $data->zona_id : null;
            
                    }
            
                    //validaciones de requeridos
                        $errores = [];

                        if (!isset($_viajeId) || $_viajeId =="" || $_viajeId == null) {
                            $errores[] = 'El campo viaje_id es requerido';
                        }
                        if (!isset($_zonaId) || $_zonaId =="" || $_zonaId == null) {
                            $errores[] = 'El campo zona_id es requerido';
                        }
            
                        if (count($errores) > 0) {
                            $respuesta->estado = "error";
                            $respuesta->respuesta = "detalle errores";
                            $respuesta->mensaje = $errores;
                            return $respuesta;
                        }
                    //fin validaciones de requeridos
            
                    // if ($_POST) {
                        // validar nro de viaje
                        $viajeID = 0;
                            $viaje = Viajes::findOne($_viajeId);
                            if (!$viaje) {
                                $respuesta->estado = "error";
                                $respuesta->respuesta = "No existe ningun viaje con este identificador";
                                $respuesta->mensaje = [];
                                return $respuesta;
                            }else{
                                $clienteId = $viaje->cliente_id;
                                $viajeID = $viaje->id;
                            }
                            
                        // fin validar nro de viaje
                        
                        // validar nro de viaje
                            $viajeDetalle = ViajeDetalle::find()->where(["viaje_id" => $viajeID, "zona_id" => $_zonaId])->one();
                            if (!$viajeDetalle) {
                                $respuesta->estado = "error";
                                $respuesta->respuesta = "No existe la zona asociada a ese viaje";
                                $respuesta->mensaje = [];
                                return $respuesta;
                            }else{
                                $zonaID = $viajeDetalle->zona_id;
                            }
                            
                        // fin validar nro de viaje
            

                    // }


                    $paradaAeliminar = ViajeDetalle::find()->where(["viaje_id" => $viajeID, "zona_id" => $zonaID])->one();
                    
                    $idParadaElimianr = $paradaAeliminar->id;
                    if ($_POST) {
                        $datos = json_encode($_POST);
                    }else{
                        $datos = json_encode($data);
                    }

                    
                    if($paradaAeliminar){

                        $paradaAeliminar->delete(); 

                        $paradasModificarOrden = ViajeDetalle::find()->where(["viaje_id" => $viajeID])->orderBy(['orden' => SORT_ASC])->all();
                        
                        $i = 1;
                        foreach ($paradasModificarOrden as $k => $v) {
                            $v->orden =  $i;
                            $v->update();
                            $i++;
                        }

                        $respuesta->estado = "ok";
                        $respuesta->respuesta =  "Parada eliminada con exito: viaje_id = {$viajeID}";
                        $respuesta->mensaje = [];

                        $this->insertarLogViajes($viajeID, $datos, "Eliminación de paradas", $idParadaElimianr);
                        return $respuesta;
                    }else{
                        $respuesta->estado = "ok";
                        $respuesta->respuesta = "No existe la parada a eliminar";
                        $respuesta->mensaje = [];

                        $this->insertarLogViajes($viajeID, $datos, "Sin paradas para eliminar", null);
                        return $respuesta;
                    }

                
                    
                }else{
                    $respuesta->estado = "error";
                    $respuesta->respuesta = "API_KEY invalida";
                    $respuesta->mensaje = [];
                }
            
                return $respuesta;
                
            }
        // fin eliminar todas las paradas de un viaje

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
                            return $decodeToken;
                        }
    
                    }else{
                        $error = "Token Invalido";
                        return $this->sendRequest(400, "error", $error, [$error], []);
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
                    $this->cabecerasGET();
                        
                    date_default_timezone_set("America/Santiago");
                
                    if (isset(getallheaders()["Authorization"])) {
                        $token = getallheaders()["Authorization"];
                        $decodeToken = $this->decodeToken($token);
                        if($decodeToken->estado != "ok"){
                            return $decodeToken;
                        }
    
                    }else{                    
                        $error = "Token Invalido";
                        return $this->sendRequest(400, "error", $error, [$error], []);
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

                $transaction = $db->beginTransaction();
                try {
                    $this->cabecerasPOST();
            
                    date_default_timezone_set('America/Santiago');
    
                    $respuesta = new stdClass();
                    if (isset(getallheaders()["Authorization"])) {
                        $token = getallheaders()["Authorization"];
                        $decodeToken = $this->decodeToken($token);
                        if($decodeToken->estado != "ok"){
                            return $decodeToken;
                        }
    
                    }else{
                        $error = "Token Invalido";
                        return $this->sendRequest(400, "error", $error, [$error], []);
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
    
                    $asignarBD = Yii::$app->bermann->asignarBD($_subdominio);
                    if(!$asignarBD->asignada){
                        $error = "Subdominio invalido";
                        return $this->sendRequest(400, "error", $error, [$error], []);
                    }
    
                    
                    
                    $viajeID = $_idViaje;
    

                    $viajeDetalle = ViajeDetalle::find()->where(["id" => $_idParada, "viaje_id" => $viajeID])->one();
    
                    $pruebaEntrega = new ViajeDetallePod();
                    $pruebaEntrega->viaje_detalle_id = $viajeDetalle->id;
                    $pruebaEntrega->nombre_firma = $_nombreFirma;
                    $pruebaEntrega->rut_firma = $_rutFirma;
                    $pruebaEntrega->empresa_firma = $_empresaFirma;
                    $pruebaEntrega->estatus_pod_id = $_estatusPod;
                    $pruebaEntrega->fecha_creado = date("Y-m-d H:i:s");
    
                    if ($pruebaEntrega->save()) {
    
                        // se envio el POD a onesigth
                        $resPOD = $this->enviarPodOS($viajeID);

                        //  validar fotos

                            $ii = 0;
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
                                $nombrefoto = 'pod_'.$_idParada.'_'.date("Ymdhis").$ii.'.png';
                                //se guarda la iamgen en el directorio correspondiente
                                if (imagepng($foto, Yii::getAlias('@webroot/documentos/viajes/pod/'.$viajeID.'/').$nombrefoto, 9)) {

                                    $viajePodDetalleImagenes = new ViajeDetallePodDetalle();
    
                                    $viajePodDetalleImagenes->viaje_detalle_pod_id = $pruebaEntrega->id;
                                    $viajePodDetalleImagenes->foto = $nombrefoto;
                                    $viajePodDetalleImagenes->validado = 0;
                                    $viajePodDetalleImagenes->save();
                                }

                                $ii++;
                            }

                        // fin validar fotos
    
                        // $resPOD = json_decode($resPOD["respone"], true);
    
                        // $this->insertarLogViajes($viajeID, "{}", "Se agrego desde APP movil", $viajeDetalle->id)
                        // if($resPOD["status"] == 200){
                        //     $this->insertarLogViajes($viajeID, "{}", "Se agrego POD a OneSight", $viajeDetalle->id)
                        // }
    
                        //******************************************************** */ insertar log y auditoria    
                        $transaction->commit();                    
                        return $this->sendRequest(200, "ok", "Se agregó POD con éxito", [], []);
                    }else{
                        // return $pruebaEntrega->getErrors();
                        $error = "Ha ocurrido un error al guardar POD";
                        return $this->sendRequest(400, "error", $error, [$error], []);
                    }
                } catch (\Throwable $th) {
                    $transaction->rollback();
                    $error = $th->getMessage();
                    return $this->sendRequest(500, "error", "Ha ocurrido un error en el servidor al procesar la solicitud", [$error], []);
                }
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
                            return $decodeToken;
                        }
    
                    }else{
                        $error = "Token Invalido";
                        return $this->sendRequest(400, "error", $error, [$error], []);
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
                            return $decodeToken;
                        }
    
                    }else{
                        $error = "Token Invalido";
                        return $this->sendRequest(400, "error", $error, [$error], []);
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
                            return $decodeToken;
                        }
    
                    }else{
                        $error = "Token Invalido";
                        return $this->sendRequest(400, "error", $error, [$error], []);
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
                            return $decodeToken;
                        }
                    }else{
                        $error = "Token Invalido";
                        return $this->sendRequest(400, "error", $error, [$error], []);
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
                        return $decodeToken;
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
                        return $decodeToken;
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
                        return $decodeToken;
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


    // ////////////////////////////////////////////////// REPORTES ///////////////////////////////////////////////////

            // 

    // ////////////////////////////////////////////////// FIN REPORTES ///////////////////////////////////////////////////



    // ///////////////////////////////////////////////// API EXTERNAS ////////////////////////////////////////////////////

        //vehiculos tms a taller o neumaticos


    // ///////////////////////////////////////////////// FIN API EXTERNAS ////////////////////////////////////////////////////


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

        public function getToken(){

            $tiempoCreacion = time(); //tiempo en que se creo el JWT
            $exp = $tiempoCreacion + 60 * 60; //expiracion del token
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
            header('Access-Control-Request-Headers: *');
            header('Access-Control-Allow-Headers: *');
            header('Access-Control-Allow-Methods: GET');
        }


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
        
    // fin  funciones complementarias

}
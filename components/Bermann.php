<?php

namespace app\components;

use Yii;
use Mandrill;
use stdClass;
use app\models\Viajes;
use app\models\Eventos;
use yii\base\Component;
use app\models\Contrato;
use app\models\Auditoria;
use app\models\LogSesion;
use app\models\Vehiculos;
use app\models\Propiedades;
use app\models\ContratoRuta;
use app\models\ViajeDetalle;
use app\models\LogVisitaVista;
use app\models\PermisosUsuario;
use app\models\RutaPropiedades;
use app\models\ConfiguracionGlobal;
use app\models\PermisosTipoUsuario;
use yii\base\InvalidConfigException;

class Bermann extends Component{
    public $api_rest;
    public $api_timeout;

    public function __construct(){}

    public function asignarBD($subdominio){

        $res = new stdClass();
        $res->urlRecursosExternos = "";
        $res->asignada = true;

        switch ($subdominio) {
            case 'interandinos':
                $res->urlRecursosExternos = "https://interandinos.bermanntms.cl/";
                Yii::$app->set('db', Yii::$app->db);
            break;
            case 'interandinospre':
                $res->urlRecursosExternos = "https://interandinospre.bermanntms.cl/";
                Yii::$app->set('db', Yii::$app->db_interandinos_pre);
            break;
            case 'interandinosqa':
                $res->urlRecursosExternos = "https://interandinosqa.bermanntms.cl/";
                Yii::$app->set('db', Yii::$app->db_interandinos_qa);
            break;
            default:
                $res->asignada = false;
            break;
        }

        return $res;
    }

}

<?php

namespace app\components;

use Yii;
use Mandrill;
use stdClass;
use Aws\S3\S3Client;
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
        $res->carpetaSpaceCliente = true;

        switch ($subdominio) {
            case 'interandinos':
                $res->asignada = false; //esto se debe eliminar cuando se suba a produccion
                $res->urlRecursosExternos = "https://interandinos.bermanntms.cl/";
                $res->carpetaSpaceCliente = "interandinos/";
                Yii::$app->set('db', Yii::$app->db);
            break;
            case 'interandinospre':
                $res->urlRecursosExternos = "https://interandinospre.bermanntms.cl/";
                $res->carpetaSpaceCliente = "interandinospre/";
                Yii::$app->set('db', Yii::$app->db_interandinos_pre);
            break;
            case 'interandinosqa':
                $res->urlRecursosExternos = "https://interandinosqa.bermanntms.cl/";
                $res->carpetaSpaceCliente = "interandinosqa/";
                Yii::$app->set('db', Yii::$app->db_interandinos_qa);
            break;
            default:
                $res->asignada = false;
            break;
        }

        // echo "<pre>";
        // var_dump(Yii::$app->getDb());
        // exit;

        return $res;
    }

    public function setSpaces(){
        $client = new S3Client([
            'version' => 'latest',
            'region'  => 'us-east-1',
            'endpoint' => 'https://nyc3.digitaloceanspaces.com',
            'use_path_style_endpoint' => false, // Configures to use subdomain/virtual calling format.
            'credentials' => [
                    'key'    => "DO00YJUHRXYPA6ZP7VML",
                    'secret' => "7yd/v7ydKoHkG6Xe/dqQyFXxjN/rSrRzU75z3HeVmWU",
                ],
        ]);

        return $client;
    }

    public function saveImagenSpaces($nombreFoto, $subdominio){
        try {
            $carpetaSpaceCliente = $this->asignarBD($subdominio)->carpetaSpaceCliente;

            $client = $this->setSpaces();
    
            $cmd = $client->putObject([
                'Bucket' => 'tmscdn',
                'Key'    => $carpetaSpaceCliente.$nombreFoto,
                'SourceFile' => Yii::getAlias('@webroot/images/') . $nombreFoto,
                'ACL'    => 'public-read'
            ]);
            return 1;
        } catch (\Throwable $th) {
           return 0;
        }
    }

    public function getImagenSpaces($nombreFoto, $subdominio){

        $res  = new stdClass();
        $res->codigo = 0;
        $res->url = "";
        try {
            $carpetaSpaceCliente = $this->asignarBD($subdominio)->carpetaSpaceCliente;

            $spaces = $this->setSpaces();

            $cmd = $spaces->getCommand('GetObject', [
                'Bucket' => 'tmscdn',
                'Key'    => $carpetaSpaceCliente.$nombreFoto
            ]);

            $imageUrl = $spaces->getObjectUrl("tmscdn", $carpetaSpaceCliente.$nombreFoto);
    

            // $request = $spaces->createPresignedRequest($cmd, '+60 minutes');
            // $presignedUrl = (string)$request->getUri();

            $res->codigo = 1;
            $res->url = $imageUrl;
            return json_encode($res);

        } catch (\Throwable $th) {

            echo "<pre>";
            var_dump($th);
            exit;
            return json_encode($res);
        }

        

    }

}

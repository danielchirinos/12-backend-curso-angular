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
                
                $res->asignada = false; TODO://esto se debe eliminar cuando se suba a produccion
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
                    'key'    => Yii::$app->params['keySpacesDo'],
                    'secret' => Yii::$app->params['secreKeySpacesDo'],
                ],
        ]);

        return $client;
    }

    private function getUrlSpaces($subdominio, $categoria){

        
        // primer nivel de la carpeta por año
        $year = date("Y_m")."/";
        
        // nombre del cliente donde se guardara las imagenes
        $carpetaSpaceCliente = $this->asignarBD($subdominio)->carpetaSpaceCliente;

        // la categoria es la carpeta donde se guardaran los datos, debe venir con /
        // ejemplo:
        // pod/, novedades/, etc

        $url = $year.$carpetaSpaceCliente.$categoria;

        return $url;
    }

    public function saveImagenSpaces($nombreFoto, $subdominio, $categoria){
        try {
            
            $urlSpaces = $this->getUrlSpaces($subdominio, $categoria);

            $client = $this->setSpaces();

            $parthLocal = Yii::getAlias('@webroot/images/') . $nombreFoto;
    
            $cmd = $client->putObject([
                'Bucket' => 'tmscdn',
                'Key'    => $urlSpaces.$nombreFoto,
                'SourceFile' => $parthLocal,
                'ACL'    => 'public-read'
            ]);

            // se elimina la imagen local despues de subirla al spaces
            unlink($parthLocal);

            return 1;
        } catch (\Throwable $th) {
           return 0;
        }
    }

    public function getImagenSpaces($nombreFoto, $subdominio, $categoria){

        $res  = new stdClass();
        $res->codigo = 0;
        $res->url = "";
        try {

            $urlSpaces = $this->getUrlSpaces($subdominio, $categoria);

            $spaces = $this->setSpaces();

            $cmd = $spaces->getCommand('GetObject', [
                'Bucket' => 'tmscdn',
                'Key'    => $urlSpaces.$nombreFoto
            ]);

            $imageUrl = $spaces->getObjectUrl("tmscdn", $urlSpaces.$nombreFoto);
    

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

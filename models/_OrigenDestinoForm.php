<?php

namespace app\models;

use Yii;
use yii\base\Model;

class _OrigenDestinoForm extends Model
{
    public $origen;
    public $fechaEntradaOrigen;
    public $fechaSalidaOrigen;
    public $destino;
    public $fechaEntradaDestino;
    public $fechaSalidaDestino;

   
    public function rules()
    {
        return [
            [['origen', 'destino'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'origen' => 'Origen',
            'fechaEntradaOrigen' => 'Fecha Hora Entrada Origen',
            'fechaSalidaOrigen' => 'Fecha Hora Salida Origen',
            'destino' => 'Destino',
            'fechaEntradaDestino' => 'Fecha Hora Entrada Destino',
            'fechaSalidaDestino' => 'Fecha Hora Salida Destino',
        ];
    }


}
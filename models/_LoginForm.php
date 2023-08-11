<?php

namespace app\models;

use Yii;
use yii\base\Model;

class _LoginForm extends Model
{
    public $usuario;
    public $clave;

   
    public function rules()
    {
        return [
            [['usuario', 'clave'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'usuario' => 'Usuario',
            'clave' => 'Clave',
        ];
    }


}
<?php

namespace app\models;

use Yii;
use yii\base\Model;

class Login extends Model
{

    public $Usuario;
    public $Clave;
    public function rules()
    {
        return [
            [['Usuario', 'Clave'], 'required'],
            [['Usuario', 'Clave'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'Usuario' => 'Usuario',
            'Clave' => 'Clave',
        ];
    }
}
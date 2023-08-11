<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "regiones".
 *
 * @property string $id
 * @property string $nombre
 * @property string $nombre_corto
 */
class Regiones extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'regiones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'nombre_corto'], 'required'],
            [['nombre', 'nombre_corto'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'nombre_corto' => 'Nombre Corto',
        ];
    }
}

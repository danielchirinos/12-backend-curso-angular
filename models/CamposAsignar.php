<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "campos_asignar".
 *
 * @property int $id
 * @property string $nombre
 * @property string $nombre_bd
 */
class CamposAsignar extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'campos_asignar';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'nombre_bd'], 'required'],
            [['nombre', 'nombre_bd'], 'string', 'max' => 255],
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
            'nombre_bd' => 'Nombre Bd',
        ];
    }
}
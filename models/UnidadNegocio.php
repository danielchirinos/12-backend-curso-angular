<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "unidad_negocio".
 *
 * @property int $id
 * @property string $nombre
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 */
class UnidadNegocio extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'unidad_negocio';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'fecha_creacion'], 'required'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['nombre'], 'string', 'max' => 255],
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
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
        ];
    }
}

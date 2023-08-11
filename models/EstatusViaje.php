<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "estatus_viaje".
 *
 * @property string $id
 * @property string $nombre
 * @property int $app
 * @property int $novedades
 * @property int $web
 * @property string $fecha_creacion
 * @property string $fecha_edicion
 * @property string $fecha_borrado
 */
class EstatusViaje extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'estatus_viaje';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre'], 'required'],
            [['app', 'novedades', 'web'], 'default', 'value' => null],
            [['app', 'novedades', 'web'], 'integer'],
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
            'app' => 'App',
            'novedades' => 'Novedades',
            'web' => 'Web',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
        ];
    }
}

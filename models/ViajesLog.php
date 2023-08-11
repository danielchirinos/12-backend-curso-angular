<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "viajes_log".
 *
 * @property int $id
 * @property int $viaje_id
 * @property int $estatus_viaje_id
 * @property int $usuario_id
 * @property string $valores_antiguos
 * @property string $valores_nuevos
 * @property string $foto
 * @property string $observaciones
 * @property string $fecha_actualizacion
 */
class ViajesLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'viajes_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['viaje_id', 'estatus_viaje_id', 'usuario_id', 'valores_nuevos'], 'required'],
            [['viaje_id', 'estatus_viaje_id', 'usuario_id'], 'default', 'value' => null],
            [['viaje_id', 'estatus_viaje_id', 'usuario_id'], 'integer'],
            [['valores_antiguos', 'valores_nuevos', 'foto', 'observaciones'], 'string'],
            [['fecha_actualizacion'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'viaje_id' => 'Viaje ID',
            'estatus_viaje_id' => 'Estatus Viaje ID',
            'usuario_id' => 'Usuario ID',
            'valores_antiguos' => 'Valores Antiguos',
            'valores_nuevos' => 'Valores Nuevos',
            'foto' => 'Foto',
            'observaciones' => 'Observaciones',
            'fecha_actualizacion' => 'Fecha Actualizacion',
        ];
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "viaje_detalle_rampla".
 *
 * @property int $id
 * @property int $viaje_detalle_id
 * @property int $estado_rampla_id
 * @property string|null $rampla
 * @property string|null $rampla_correcta
 * @property string|null $observaciones
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_error
 * @property string|null $fecha_borrado
 *
 * @property EstadoRampla $estadoRampla
 * @property ViajeDetalle $viajeDetalle
 */
class ViajeDetalleRampla extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'viaje_detalle_rampla';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['viaje_detalle_id', 'estado_rampla_id', 'fecha_creacion'], 'required'],
            [['viaje_detalle_id', 'estado_rampla_id'], 'default', 'value' => null],
            [['viaje_detalle_id', 'estado_rampla_id'], 'integer'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_error', 'fecha_borrado'], 'safe'],
            [['rampla', 'rampla_correcta', 'observaciones'], 'string', 'max' => 255],
            [['estado_rampla_id'], 'exist', 'skipOnError' => true, 'targetClass' => EstadoRampla::className(), 'targetAttribute' => ['estado_rampla_id' => 'id']],
            [['viaje_detalle_id'], 'exist', 'skipOnError' => true, 'targetClass' => ViajeDetalle::className(), 'targetAttribute' => ['viaje_detalle_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'viaje_detalle_id' => 'Viaje Detalle ID',
            'estado_rampla_id' => 'Estado Rampla ID',
            'rampla' => 'Rampla',
            'rampla_correcta' => 'Rampla Correcta',
            'observaciones' => 'Observaciones',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_error' => 'Fecha Error',
            'fecha_borrado' => 'Fecha Borrado',
        ];
    }

    /**
     * Gets query for [[EstadoRampla]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEstadoRampla()
    {
        return $this->hasOne(EstadoRampla::className(), ['id' => 'estado_rampla_id']);
    }

    /**
     * Gets query for [[ViajeDetalle]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajeDetalle()
    {
        return $this->hasOne(ViajeDetalle::className(), ['id' => 'viaje_detalle_id']);
    }
}

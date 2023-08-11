<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "viaje_novedades".
 *
 * @property int $id
 * @property int $viaje_detalle_id
 * @property int $subestatus_viaje_id
 * @property string $fotos
 * @property string $observaciones
 * @property string $fecha_creacion
 *
 * @property SubestatusViaje $subestatusViaje
 * @property ViajeDetalle $viajeDetalle
 */
class ViajeNovedades extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'viaje_novedades';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['viaje_detalle_id'], 'required'],
            [['viaje_detalle_id', 'subestatus_viaje_id'], 'default', 'value' => null],
            [['viaje_detalle_id', 'subestatus_viaje_id'], 'integer'],
            [['fotos', 'observaciones'], 'string'],
            [['fecha_creacion'], 'safe'],
            [['subestatus_viaje_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubestatusViaje::className(), 'targetAttribute' => ['subestatus_viaje_id' => 'id']],
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
            'subestatus_viaje_id' => 'Subestatus Viaje ID',
            'fotos' => 'Fotos',
            'observaciones' => 'Observaciones',
            'fecha_creacion' => 'Fecha Creacion',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubestatusViaje()
    {
        return $this->hasOne(SubestatusViaje::className(), ['id' => 'subestatus_viaje_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getViajeDetalle()
    {
        return $this->hasOne(ViajeDetalle::className(), ['id' => 'viaje_detalle_id']);
    }
}

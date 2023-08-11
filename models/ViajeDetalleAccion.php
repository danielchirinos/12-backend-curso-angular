<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "viaje_detalle_accion".
 *
 * @property int $id
 * @property int $viaje_detalle_id
 * @property int $accion_id
 * @property int $conductor_id
 * @property string $latitud
 * @property string $longitud
 * @property string $fecha
 * @property string $fecha_creacion
 *
 * @property ViajeDetalle[] $viajeDetalles
 * @property Accion $accion
 * @property Conductores $conductor
 * @property ViajeDetalle $viajeDetalle
 */
class ViajeDetalleAccion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'viaje_detalle_accion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['viaje_detalle_id', 'accion_id', 'conductor_id', 'latitud', 'longitud', 'fecha', 'fecha_creacion'], 'required'],
            [['viaje_detalle_id', 'accion_id', 'conductor_id'], 'default', 'value' => null],
            [['viaje_detalle_id', 'accion_id', 'conductor_id'], 'integer'],
            [['fecha', 'fecha_creacion'], 'safe'],
            [['latitud', 'longitud'], 'string', 'max' => 255],
            [['accion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Accion::className(), 'targetAttribute' => ['accion_id' => 'id']],
            [['conductor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Conductores::className(), 'targetAttribute' => ['conductor_id' => 'id']],
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
            'accion_id' => 'Accion ID',
            'conductor_id' => 'Conductor ID',
            'latitud' => 'Latitud',
            'longitud' => 'Longitud',
            'fecha' => 'Fecha',
            'fecha_creacion' => 'Fecha Creacion',
        ];
    }

    /**
     * Gets query for [[ViajeDetalles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajeDetalles()
    {
        return $this->hasMany(ViajeDetalle::className(), ['viaje_detalle_accion_id' => 'id']);
    }

    /**
     * Gets query for [[Accion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccion()
    {
        return $this->hasOne(Accion::className(), ['id' => 'accion_id']);
    }

    /**
     * Gets query for [[Conductor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getConductor()
    {
        return $this->hasOne(Conductores::className(), ['id' => 'conductor_id']);
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

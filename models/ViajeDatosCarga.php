<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "viaje_datos_carga".
 *
 * @property int $id
 * @property int $viaje_id
 * @property int $viaje_detalle_id
 * @property int $conductor_id
 * @property string $nombre_campo
 * @property string $valor_campo
 * @property string|null $error_campo
 * @property string|null $observaciones
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_error
 *
 * @property Conductores $conductor
 * @property ViajeDetalle $viajeDetalle
 * @property Viajes $viaje
 */
class ViajeDatosCarga extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'viaje_datos_carga';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['viaje_id', 'viaje_detalle_id', 'conductor_id', 'nombre_campo', 'valor_campo', 'fecha_creacion'], 'required'],
            [['viaje_id', 'viaje_detalle_id', 'conductor_id'], 'default', 'value' => null],
            [['viaje_id', 'viaje_detalle_id', 'conductor_id'], 'integer'],
            [['observaciones'], 'string'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_error'], 'safe'],
            [['nombre_campo', 'valor_campo', 'error_campo'], 'string', 'max' => 255],
            [['conductor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Conductores::className(), 'targetAttribute' => ['conductor_id' => 'id']],
            [['viaje_detalle_id'], 'exist', 'skipOnError' => true, 'targetClass' => ViajeDetalle::className(), 'targetAttribute' => ['viaje_detalle_id' => 'id']],
            [['viaje_id'], 'exist', 'skipOnError' => true, 'targetClass' => Viajes::className(), 'targetAttribute' => ['viaje_id' => 'id']],
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
            'viaje_detalle_id' => 'Viaje Detalle ID',
            'conductor_id' => 'Conductor ID',
            'nombre_campo' => 'Nombre Campo',
            'valor_campo' => 'Valor Campo',
            'error_campo' => 'Error Campo',
            'observaciones' => 'Observaciones',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_error' => 'Fecha Error',
        ];
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

    /**
     * Gets query for [[Viaje]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViaje()
    {
        return $this->hasOne(Viajes::className(), ['id' => 'viaje_id']);
    }
}

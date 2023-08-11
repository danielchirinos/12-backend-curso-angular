<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "viaje_documento".
 *
 * @property int $id
 * @property int $viaje_id
 * @property int $viaje_detalle_id
 * @property int|null $documento_id
 * @property string $documento
 * @property int $conductor_id
 * @property string|null $observaciones
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 *
 * @property Documento $documento0
 * @property ViajeDetalle $viajeDetalle
 * @property Viajes $viaje
 */
class ViajeDocumento extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'viaje_documento';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['viaje_id', 'viaje_detalle_id', 'documento', 'conductor_id', 'fecha_creacion'], 'required'],
            [['viaje_id', 'viaje_detalle_id', 'documento_id', 'conductor_id'], 'default', 'value' => null],
            [['viaje_id', 'viaje_detalle_id', 'documento_id', 'conductor_id'], 'integer'],
            [['documento'], 'string'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['observaciones'], 'string', 'max' => 255],
            [['documento_id'], 'exist', 'skipOnError' => true, 'targetClass' => Documento::className(), 'targetAttribute' => ['documento_id' => 'id']],
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
            'documento_id' => 'Documento ID',
            'documento' => 'Documento',
            'conductor_id' => 'Conductor ID',
            'observaciones' => 'Observaciones',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
        ];
    }

    /**
     * Gets query for [[Documento0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumento0()
    {
        return $this->hasOne(Documento::className(), ['id' => 'documento_id']);
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

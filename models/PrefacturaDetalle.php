<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "prefactura_detalle".
 *
 * @property int $id
 * @property int $prefactura_id
 * @property int $viaje_id
 * @property string $nombre_servicio
 *
 * @property Prefactura $prefactura
 * @property Viajes $viaje
 */
class PrefacturaDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'prefactura_detalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['prefactura_id', 'viaje_id', 'nombre_servicio'], 'required'],
            [['prefactura_id', 'viaje_id'], 'default', 'value' => null],
            [['prefactura_id', 'viaje_id'], 'integer'],
            [['nombre_servicio'], 'string', 'max' => 255],
            [['prefactura_id'], 'exist', 'skipOnError' => true, 'targetClass' => Prefactura::className(), 'targetAttribute' => ['prefactura_id' => 'id']],
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
            'prefactura_id' => 'Prefactura ID',
            'viaje_id' => 'Viaje ID',
            'nombre_servicio' => 'Nombre Servicio',
        ];
    }

    /**
     * Gets query for [[Prefactura]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrefactura()
    {
        return $this->hasOne(Prefactura::className(), ['id' => 'prefactura_id']);
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

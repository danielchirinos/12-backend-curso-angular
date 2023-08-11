<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "prefactura_servicio_adicional".
 *
 * @property int $id
 * @property int|null $prefactura_id
 * @property int|null $servicio_adicional_id
 * @property float|null $tarifa
 * @property string|null $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 * @property string|null $origen
 *
 * @property Prefactura $prefactura
 * @property ServicioAdicionales $servicioAdicional
 */
class PrefacturaServicioAdicional extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'prefactura_servicio_adicional';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['prefactura_id', 'servicio_adicional_id'], 'default', 'value' => null],
            [['prefactura_id', 'servicio_adicional_id'], 'integer'],
            [['tarifa'], 'number'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['origen'], 'string', 'max' => 255],
            [['prefactura_id'], 'exist', 'skipOnError' => true, 'targetClass' => Prefactura::className(), 'targetAttribute' => ['prefactura_id' => 'id']],
            [['servicio_adicional_id'], 'exist', 'skipOnError' => true, 'targetClass' => ServicioAdicionales::className(), 'targetAttribute' => ['servicio_adicional_id' => 'id']],
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
            'servicio_adicional_id' => 'Servicio Adicional ID',
            'tarifa' => 'Tarifa',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
            'origen' => 'Origen',
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
     * Gets query for [[ServicioAdicional]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getServicioAdicional()
    {
        return $this->hasOne(ServicioAdicionales::className(), ['id' => 'servicio_adicional_id']);
    }
}

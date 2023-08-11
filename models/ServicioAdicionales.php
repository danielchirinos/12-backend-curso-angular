<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "servicio_adicionales".
 *
 * @property int $id
 * @property int $tipo_cobro_id
 * @property string|null $servicio
 * @property float|null $tarifa_costo_base
 * @property float|null $tarifa_venta_base
 * @property int|null $tipo_moneda_id
 * @property string|null $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 * @property int|null $tipo_cambio
 *
 * @property ContratoServicioAdicional[] $contratoServicioAdicionals
 * @property PrefacturaServicioAdicional[] $prefacturaServicioAdicionals
 * @property ProformaServicioAdicional[] $proformaServicioAdicionals
 * @property TipoCambio $tipoCambio
 * @property TipoCobro $tipoCobro
 * @property TipoMoneda $tipoMoneda
 * @property ViajesServiciosAdicionales[] $viajesServiciosAdicionales
 */
class ServicioAdicionales extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'servicio_adicionales';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipo_cobro_id'], 'required'],
            [['tipo_cobro_id', 'tipo_moneda_id', 'tipo_cambio'], 'default', 'value' => null],
            [['tipo_cobro_id', 'tipo_moneda_id', 'tipo_cambio'], 'integer'],
            [['servicio'], 'string'],
            [['tarifa_costo_base', 'tarifa_venta_base'], 'number'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['tipo_cambio'], 'exist', 'skipOnError' => true, 'targetClass' => TipoCambio::className(), 'targetAttribute' => ['tipo_cambio' => 'id']],
            [['tipo_cobro_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoCobro::className(), 'targetAttribute' => ['tipo_cobro_id' => 'id']],
            [['tipo_moneda_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoMoneda::className(), 'targetAttribute' => ['tipo_moneda_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tipo_cobro_id' => 'Tipo Cobro ID',
            'servicio' => 'Servicio',
            'tarifa_costo_base' => 'Tarifa Costo Base',
            'tarifa_venta_base' => 'Tarifa Venta Base',
            'tipo_moneda_id' => 'Tipo Moneda ID',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
            'tipo_cambio' => 'Tipo Cambio',
        ];
    }

    /**
     * Gets query for [[ContratoServicioAdicionals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContratoServicioAdicionals()
    {
        return $this->hasMany(ContratoServicioAdicional::className(), ['servicio_adicional_id' => 'id']);
    }

    /**
     * Gets query for [[PrefacturaServicioAdicionals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrefacturaServicioAdicionals()
    {
        return $this->hasMany(PrefacturaServicioAdicional::className(), ['servicio_adicional_id' => 'id']);
    }

    /**
     * Gets query for [[ProformaServicioAdicionals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProformaServicioAdicionals()
    {
        return $this->hasMany(ProformaServicioAdicional::className(), ['servicio_adicional_id' => 'id']);
    }

    /**
     * Gets query for [[TipoCambio]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTipoCambio()
    {
        return $this->hasOne(TipoCambio::className(), ['id' => 'tipo_cambio']);
    }

    /**
     * Gets query for [[TipoCobro]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTipoCobro()
    {
        return $this->hasOne(TipoCobro::className(), ['id' => 'tipo_cobro_id']);
    }

    /**
     * Gets query for [[TipoMoneda]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTipoMoneda()
    {
        return $this->hasOne(TipoMoneda::className(), ['id' => 'tipo_moneda_id']);
    }

    /**
     * Gets query for [[ViajesServiciosAdicionales]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajesServiciosAdicionales()
    {
        return $this->hasMany(ViajesServiciosAdicionales::className(), ['servicio_adicional_id' => 'id']);
    }
}

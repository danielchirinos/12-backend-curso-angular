<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ruta".
 *
 * @property int $id
 * @property string $nombre_ruta
 * @property int $tipo_vehiculo_id
 * @property int $tipo_rampla_id
 * @property int $uso_chasis_id
 * @property int $unidad_negocio_id
 * @property string|null $tamano
 * @property string|null $km_total
 * @property int|null $tarifa
 * @property string|null $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 *
 * @property ContratoRuta[] $contratoRutas
 * @property TipoRampla $tipoRampla
 * @property TipoVehiculos $tipoVehiculo
 * @property UnidadNegocio $unidadNegocio
 * @property UsoChasis $usoChasis
 * @property RutaDetalle[] $rutaDetalles
 * @property Viajes[] $viajes
 */
class Ruta extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ruta';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre_ruta', 'tipo_vehiculo_id', 'tipo_rampla_id', 'uso_chasis_id', 'unidad_negocio_id'], 'required'],
            [['tipo_vehiculo_id', 'tipo_rampla_id', 'uso_chasis_id', 'unidad_negocio_id', 'tarifa'], 'default', 'value' => null],
            [['tipo_vehiculo_id', 'tipo_rampla_id', 'uso_chasis_id', 'unidad_negocio_id', 'tarifa'], 'integer'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['nombre_ruta', 'tamano', 'km_total'], 'string', 'max' => 255],
            [['tipo_rampla_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoRampla::className(), 'targetAttribute' => ['tipo_rampla_id' => 'id']],
            [['tipo_vehiculo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoVehiculos::className(), 'targetAttribute' => ['tipo_vehiculo_id' => 'id']],
            [['unidad_negocio_id'], 'exist', 'skipOnError' => true, 'targetClass' => UnidadNegocio::className(), 'targetAttribute' => ['unidad_negocio_id' => 'id']],
            [['uso_chasis_id'], 'exist', 'skipOnError' => true, 'targetClass' => UsoChasis::className(), 'targetAttribute' => ['uso_chasis_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre_ruta' => 'Nombre Ruta',
            'tipo_vehiculo_id' => 'Tipo Vehiculo ID',
            'tipo_rampla_id' => 'Tipo Rampla ID',
            'uso_chasis_id' => 'Uso Chasis ID',
            'unidad_negocio_id' => 'Unidad Negocio ID',
            'tamano' => 'Tamano',
            'km_total' => 'Km Total',
            'tarifa' => 'Tarifa',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
        ];
    }

    /**
     * Gets query for [[ContratoRutas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContratoRutas()
    {
        return $this->hasMany(ContratoRuta::className(), ['ruta_id' => 'id']);
    }

    /**
     * Gets query for [[TipoRampla]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTipoRampla()
    {
        return $this->hasOne(TipoRampla::className(), ['id' => 'tipo_rampla_id']);
    }

    /**
     * Gets query for [[TipoVehiculo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTipoVehiculo()
    {
        return $this->hasOne(TipoVehiculos::className(), ['id' => 'tipo_vehiculo_id']);
    }

    /**
     * Gets query for [[UnidadNegocio]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUnidadNegocio()
    {
        return $this->hasOne(UnidadNegocio::className(), ['id' => 'unidad_negocio_id']);
    }

    /**
     * Gets query for [[UsoChasis]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsoChasis()
    {
        return $this->hasOne(UsoChasis::className(), ['id' => 'uso_chasis_id']);
    }

    /**
     * Gets query for [[RutaDetalles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRutaDetalles()
    {
        return $this->hasMany(RutaDetalle::className(), ['ruta_id' => 'id']);
    }

    /**
     * Gets query for [[Viajes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajes()
    {
        return $this->hasMany(Viajes::className(), ['ruta_id' => 'id']);
    }
}

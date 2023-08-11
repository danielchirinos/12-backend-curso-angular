<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "vehiculos".
 *
 * @property int $id
 * @property int $transportista_id
 * @property int $tipo_vehiculo_id
 * @property string $patente
 * @property string $muestra
 * @property string $av_serie
 * @property string|null $horometro
 * @property string|null $odometro
 * @property string|null $fecha_transmision
 * @property string|null $latitud
 * @property string|null $longitud
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 * @property string|null $nro_chasis guarda el nro de chasis para fullttuck
 * @property string|null $orientacion orientacion del vehiculo desde la ultima fecha de trasnmision
 * @property int|null $tipo_rampla_id
 * @property int $gps 0 no teine gps / 1 con gps
 *
 * @property ConductorLibreta[] $conductorLibretas
 * @property EventosRs[] $eventosRs
 * @property EventosTemperatura[] $eventosTemperaturas
 * @property EventosTemperaturaCiclos[] $eventosTemperaturaCiclos
 * @property HojaRuta[] $hojaRutas
 * @property VehiculoPropiedad[] $vehiculoPropiedads
 * @property TipoVehiculos $tipoVehiculo
 * @property Transportistas $transportista
 * @property Viajes[] $viajes
 * @property Viajes[] $viajes0
 */
class Vehiculos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'vehiculos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['transportista_id', 'tipo_vehiculo_id', 'patente', 'muestra', 'av_serie', 'fecha_creacion'], 'required'],
            [['transportista_id', 'tipo_vehiculo_id', 'tipo_rampla_id', 'gps'], 'default', 'value' => null],
            [['transportista_id', 'tipo_vehiculo_id', 'tipo_rampla_id', 'gps'], 'integer'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['patente', 'muestra', 'av_serie', 'horometro', 'odometro', 'fecha_transmision', 'latitud', 'longitud', 'nro_chasis', 'orientacion'], 'string', 'max' => 255],
            [['tipo_vehiculo_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoVehiculos::className(), 'targetAttribute' => ['tipo_vehiculo_id' => 'id']],
            [['transportista_id'], 'exist', 'skipOnError' => true, 'targetClass' => Transportistas::className(), 'targetAttribute' => ['transportista_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'transportista_id' => 'Transportista ID',
            'tipo_vehiculo_id' => 'Tipo Vehiculo ID',
            'patente' => 'Patente',
            'muestra' => 'Muestra',
            'av_serie' => 'Av Serie',
            'horometro' => 'Horometro',
            'odometro' => 'Odometro',
            'fecha_transmision' => 'Fecha Transmision',
            'latitud' => 'Latitud',
            'longitud' => 'Longitud',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
            'nro_chasis' => 'Nro Chasis',
            'orientacion' => 'Orientacion',
            'tipo_rampla_id' => 'Tipo Rampla ID',
            'gps' => 'Gps',
        ];
    }

    /**
     * Gets query for [[ConductorLibretas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getConductorLibretas()
    {
        return $this->hasMany(ConductorLibreta::className(), ['vehiculo_id' => 'id']);
    }

    /**
     * Gets query for [[EventosRs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEventosRs()
    {
        return $this->hasMany(EventosRs::className(), ['vh_id' => 'id']);
    }

    /**
     * Gets query for [[EventosTemperaturas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEventosTemperaturas()
    {
        return $this->hasMany(EventosTemperatura::className(), ['vh_id' => 'id']);
    }

    /**
     * Gets query for [[EventosTemperaturaCiclos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEventosTemperaturaCiclos()
    {
        return $this->hasMany(EventosTemperaturaCiclos::className(), ['vh_id' => 'id']);
    }

    /**
     * Gets query for [[HojaRutas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHojaRutas()
    {
        return $this->hasMany(HojaRuta::className(), ['vehiculo_uno_id' => 'id']);
    }

    /**
     * Gets query for [[VehiculoPropiedads]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVehiculoPropiedads()
    {
        return $this->hasMany(VehiculoPropiedad::className(), ['vehiculo_id' => 'id']);
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
     * Gets query for [[Transportista]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTransportista()
    {
        return $this->hasOne(Transportistas::className(), ['id' => 'transportista_id']);
    }

    /**
     * Gets query for [[Viajes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajes()
    {
        return $this->hasMany(Viajes::className(), ['vehiculo_uno_id' => 'id']);
    }

    /**
     * Gets query for [[Viajes0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajes0()
    {
        return $this->hasMany(Viajes::className(), ['vehiculo_dos_id' => 'id']);
    }
}

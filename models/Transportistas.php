<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transportistas".
 *
 * @property string $id ID
 * @property string $nombre Nombre Fantasia
 * @property string $razon_social Razón Social
 * @property string $documento RUT
 * @property int $comuna_id Comuna
 * @property int $region_id Región
 * @property int $ciudad_id Ciudad
 * @property int $tipo_transportista_id Tipo Transportista
 * @property string $calle Calle
 * @property string $altura Numeración
 * @property string $otros Otros
 * @property int $estado Estado
 * @property string $fecha_creacion
 * @property string $fecha_edicion
 * @property string $fecha_borrado
 *
 * @property Conductores[] $conductores
 * @property Ciudades $ciudad
 * @property Comunas $comuna
 * @property Regiones $region
 * @property TipoTransportista $tipoTransportista
 * @property TransportistasContactos[] $transportistasContactos
 * @property Vehiculos[] $vehiculos
 * @property Viajes[] $viajes
 */
class Transportistas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transportistas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'razon_social', 'documento', 'comuna_id', 'region_id', 'ciudad_id', 'tipo_transportista_id'], 'required'],
            [['comuna_id', 'region_id', 'ciudad_id', 'tipo_transportista_id', 'estado'], 'default', 'value' => null],
            [['comuna_id', 'region_id', 'ciudad_id', 'tipo_transportista_id', 'estado'], 'integer'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['nombre', 'razon_social', 'documento', 'calle', 'altura', 'otros'], 'string', 'max' => 255],
            [['ciudad_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ciudades::className(), 'targetAttribute' => ['ciudad_id' => 'id']],
            [['comuna_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comunas::className(), 'targetAttribute' => ['comuna_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regiones::className(), 'targetAttribute' => ['region_id' => 'id']],
            [['tipo_transportista_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoTransportista::className(), 'targetAttribute' => ['tipo_transportista_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre Fantasia',
            'razon_social' => 'Razón Social',
            'documento' => 'RUT',
            'comuna_id' => 'Comuna',
            'region_id' => 'Región',
            'ciudad_id' => 'Ciudad',
            'tipo_transportista_id' => 'Tipo Transportista',
            'calle' => 'Calle',
            'altura' => 'Numeración',
            'otros' => 'Otros',
            'estado' => 'Estado',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConductores()
    {
        return $this->hasMany(Conductores::className(), ['transportista_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCiudad()
    {
        return $this->hasOne(Ciudades::className(), ['id' => 'ciudad_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComuna()
    {
        return $this->hasOne(Comunas::className(), ['id' => 'comuna_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Regiones::className(), ['id' => 'region_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoTransportista()
    {
        return $this->hasOne(TipoTransportista::className(), ['id' => 'tipo_transportista_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransportistasContactos()
    {
        return $this->hasMany(TransportistasContactos::className(), ['transportista_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVehiculos()
    {
        return $this->hasMany(Vehiculos::className(), ['transportista_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getViajes()
    {
        return $this->hasMany(Viajes::className(), ['transportista_id' => 'id']);
    }
}

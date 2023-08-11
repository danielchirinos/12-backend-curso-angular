<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clientes".
 *
 * @property int $id
 * @property string $rut RUT
 * @property string $nombre Razón Social
 * @property string $nombre_fantasia Nombre Fantasia
 * @property int $comuna_id Comuna
 * @property int $region_id Región
 * @property int $ciudad_id Ciudad
 * @property int $tipo_cliente_id Tipo Cliente
 * @property string $calle
 * @property string $altura
 * @property string $otros
 * @property bool $dashboard
 * @property string $fecha_creacion
 * @property string $fecha_edicion
 * @property string $fecha_borrado
 * @property string $imagen
 *
 * @property Ciudades $ciudad
 * @property Comunas $comuna
 * @property Regiones $region
 * @property TipoCliente $tipoCliente
 * @property ClientesAlarmas $clientesAlarmas
 * @property ClientesContactos[] $clientesContactos
 * @property Viajes[] $viajes
 */
class Clientes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clientes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rut', 'nombre', 'nombre_fantasia', 'comuna_id', 'region_id', 'ciudad_id', 'tipo_cliente_id'], 'required'],
            [['comuna_id', 'region_id', 'ciudad_id', 'tipo_cliente_id'], 'default', 'value' => null],
            [['comuna_id', 'region_id', 'ciudad_id', 'tipo_cliente_id'], 'integer'],
            [['dashboard'], 'boolean'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['rut', 'nombre', 'nombre_fantasia', 'calle', 'altura', 'otros', 'imagen'], 'string', 'max' => 255],
            [['ciudad_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ciudades::className(), 'targetAttribute' => ['ciudad_id' => 'id']],
            [['comuna_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comunas::className(), 'targetAttribute' => ['comuna_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regiones::className(), 'targetAttribute' => ['region_id' => 'id']],
            [['tipo_cliente_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoCliente::className(), 'targetAttribute' => ['tipo_cliente_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rut' => 'Rut',
            'nombre' => 'Razón Social',
            'nombre_fantasia' => 'Nombre Fantasia',
            'comuna_id' => 'Comuna',
            'region_id' => 'Región',
            'ciudad_id' => 'Ciudad',
            'tipo_cliente_id' => 'Tipo Cliente',
            'calle' => 'Calle',
            'altura' => 'Altura',
            'otros' => 'Otros',
            'dashboard' => 'Dashboard',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
            'imagen' => 'Imagen',
        ];
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
    public function getTipoCliente()
    {
        return $this->hasOne(TipoCliente::className(), ['id' => 'tipo_cliente_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientesAlarmas()
    {
        return $this->hasOne(ClientesAlarmas::className(), ['cliente_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientesContactos()
    {
        return $this->hasMany(ClientesContactos::className(), ['cliente_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getViajes()
    {
        return $this->hasMany(Viajes::className(), ['cliente_id' => 'id']);
    }
}

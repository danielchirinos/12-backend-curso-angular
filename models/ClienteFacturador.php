<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cliente_facturador".
 *
 * @property int $id
 * @property string $rut RUT
 * @property string $nombre Razón Social
 * @property string $nombre_fantasia Nombre Fantasia
 * @property int $comuna_id Comuna
 * @property int $region_id Región
 * @property int $ciudad_id Ciudad
 * @property string|null $calle
 * @property string|null $altura
 * @property string|null $otros
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 * @property bool|null $dashboard
 * @property string|null $imagen
 *
 * @property Ciudades $ciudad
 * @property Comunas $comuna
 * @property Regiones $region
 */
class ClienteFacturador extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cliente_facturador';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rut', 'nombre', 'nombre_fantasia', 'comuna_id', 'region_id', 'ciudad_id'], 'required'],
            [['comuna_id', 'region_id', 'ciudad_id'], 'default', 'value' => null],
            [['comuna_id', 'region_id', 'ciudad_id'], 'integer'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['dashboard'], 'boolean'],
            [['rut', 'nombre', 'nombre_fantasia', 'calle', 'altura', 'otros', 'imagen'], 'string', 'max' => 255],
            [['rut'], 'unique'],
            [['ciudad_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ciudades::className(), 'targetAttribute' => ['ciudad_id' => 'id']],
            [['comuna_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comunas::className(), 'targetAttribute' => ['comuna_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regiones::className(), 'targetAttribute' => ['region_id' => 'id']],
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
            'nombre' => 'Nombre',
            'nombre_fantasia' => 'Nombre Fantasia',
            'comuna_id' => 'Comuna ID',
            'region_id' => 'Region ID',
            'ciudad_id' => 'Ciudad ID',
            'calle' => 'Calle',
            'altura' => 'Altura',
            'otros' => 'Otros',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
            'dashboard' => 'Dashboard',
            'imagen' => 'Imagen',
        ];
    }

    /**
     * Gets query for [[Ciudad]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCiudad()
    {
        return $this->hasOne(Ciudades::className(), ['id' => 'ciudad_id']);
    }

    /**
     * Gets query for [[Comuna]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComuna()
    {
        return $this->hasOne(Comunas::className(), ['id' => 'comuna_id']);
    }

    /**
     * Gets query for [[Region]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Regiones::className(), ['id' => 'region_id']);
    }
}

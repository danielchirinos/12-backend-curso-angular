<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ciudades".
 *
 * @property int $id
 * @property int $region_id
 * @property string $nombre
 *
 * @property Regiones $region
 * @property Clientes[] $clientes
 * @property Comunas[] $comunas
 * @property Transportistas[] $transportistas
 */
class Ciudades extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ciudades';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['region_id', 'nombre'], 'required'],
            [['region_id'], 'default', 'value' => null],
            [['region_id'], 'integer'],
            [['nombre'], 'string', 'max' => 255],
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
            'region_id' => 'Region ID',
            'nombre' => 'Nombre',
        ];
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
    public function getClientes()
    {
        return $this->hasMany(Clientes::className(), ['ciudad_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComunas()
    {
        return $this->hasMany(Comunas::className(), ['ciudad_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransportistas()
    {
        return $this->hasMany(Transportistas::className(), ['ciudad_id' => 'id']);
    }
}

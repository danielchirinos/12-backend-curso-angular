<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "comunas".
 *
 * @property int $id
 * @property int $ciudad_id
 * @property string $nombre
 *
 * @property Clientes[] $clientes
 * @property Ciudades $ciudad
 * @property Transportistas[] $transportistas
 */
class Comunas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comunas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ciudad_id', 'nombre'], 'required'],
            [['ciudad_id'], 'default', 'value' => null],
            [['ciudad_id'], 'integer'],
            [['nombre'], 'string', 'max' => 255],
            [['ciudad_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ciudades::className(), 'targetAttribute' => ['ciudad_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ciudad_id' => 'Ciudad',
            'nombre' => 'Nombre',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClientes()
    {
        return $this->hasMany(Clientes::className(), ['comuna_id' => 'id']);
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
    public function getTransportistas()
    {
        return $this->hasMany(Transportistas::className(), ['comuna_id' => 'id']);
    }
}

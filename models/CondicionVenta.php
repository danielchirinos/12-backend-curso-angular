<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "condicion_venta".
 *
 * @property int $id
 * @property string $descripcion
 * @property string $codigo_erp
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 * @property int $dias
 *
 * @property Contrato[] $contratos
 * @property Prefactura[] $prefacturas
 */
class CondicionVenta extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'condicion_venta';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descripcion', 'codigo_erp', 'fecha_creacion', 'dias'], 'required'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['dias'], 'default', 'value' => null],
            [['dias'], 'integer'],
            [['descripcion', 'codigo_erp'], 'string', 'max' => 255],
            [['codigo_erp'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'descripcion' => 'Descripcion',
            'codigo_erp' => 'Codigo Erp',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
            'dias' => 'Dias',
        ];
    }

    /**
     * Gets query for [[Contratos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContratos()
    {
        return $this->hasMany(Contrato::className(), ['condicion_venta_id' => 'id']);
    }

    /**
     * Gets query for [[Prefacturas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrefacturas()
    {
        return $this->hasMany(Prefactura::className(), ['condicion_venta_id' => 'id']);
    }
}

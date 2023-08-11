<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "caja".
 *
 * @property int $id
 * @property float|null $monto
 * @property string $codigo
 * @property string|null $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 * @property int|null $principal 1 para principal 0 para secundaria
 * @property string $cuenta_contable
 *
 * @property CajaVale[] $cajaVales
 */
class Caja extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'caja';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['monto'], 'number'],
            [['codigo', 'cuenta_contable'], 'required'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['principal'], 'default', 'value' => null],
            [['principal'], 'integer'],
            [['codigo', 'cuenta_contable'], 'string', 'max' => 255],
            [['codigo'], 'unique'],
            [['cuenta_contable'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'monto' => 'Monto',
            'codigo' => 'Codigo',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
            'principal' => 'Principal',
            'cuenta_contable' => 'Cuenta Contable',
        ];
    }

    /**
     * Gets query for [[CajaVales]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCajaVales()
    {
        return $this->hasMany(CajaVale::className(), ['caja_id' => 'id']);
    }
}

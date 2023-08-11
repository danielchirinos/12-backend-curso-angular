<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "liquidacion".
 *
 * @property int $id
 * @property int|null $estado 1 generado, 2 para disponible de pago, 3 para pagada
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 * @property int $cantidad_viajes
 * @property string $rango_fechas_liquidacion la fecha de inicio y fecha fin de la liquidacion
 * @property string|null $fecha_pago
 * @property float $total_comision_uno
 * @property float $total_comision_dos
 * @property int $cantidad_conductores
 *
 * @property LiquidacionDetalle[] $liquidacionDetalles
 * @property Viajes[] $viajes
 */
class Liquidacion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'liquidacion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['estado', 'cantidad_viajes', 'cantidad_conductores'], 'default', 'value' => null],
            [['estado', 'cantidad_viajes', 'cantidad_conductores'], 'integer'],
            [['fecha_creacion', 'cantidad_viajes', 'rango_fechas_liquidacion', 'total_comision_uno', 'total_comision_dos', 'cantidad_conductores'], 'required'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado', 'fecha_pago'], 'safe'],
            [['rango_fechas_liquidacion'], 'string'],
            [['total_comision_uno', 'total_comision_dos'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'estado' => 'Estado',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
            'cantidad_viajes' => 'Cantidad Viajes',
            'rango_fechas_liquidacion' => 'Rango Fechas Liquidacion',
            'fecha_pago' => 'Fecha Pago',
            'total_comision_uno' => 'Total Comision Uno',
            'total_comision_dos' => 'Total Comision Dos',
            'cantidad_conductores' => 'Cantidad Conductores',
        ];
    }

    /**
     * Gets query for [[LiquidacionDetalles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLiquidacionDetalles()
    {
        return $this->hasMany(LiquidacionDetalle::className(), ['liquidacion_id' => 'id']);
    }

    /**
     * Gets query for [[Viajes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajes()
    {
        return $this->hasMany(Viajes::className(), ['liquidacion_id' => 'id']);
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rendicion".
 *
 * @property int $id
 * @property int $viaje_id
 * @property float|null $monto_aprovisionado
 * @property float|null $monto_rendido
 * @property float|null $monto_saldo
 * @property string $fecha_creacion
 * @property int $estado 0 PENDIENTE POR RENDIR / 1 RENDIDO / 2 CERRADO ADMINISTRATIVAMENTE
 * @property int|null $estado_erp 0 para no centralizado / 1 para centralizado en erp
 * @property string|null $fecha_edicion
 *
 * @property Viajes $viaje
 * @property RendicionDetalle[] $rendicionDetalles
 */
class Rendicion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rendicion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['viaje_id', 'fecha_creacion'], 'required'],
            [['viaje_id', 'estado', 'estado_erp'], 'default', 'value' => null],
            [['viaje_id', 'estado', 'estado_erp'], 'integer'],
            [['monto_aprovisionado', 'monto_rendido', 'monto_saldo'], 'number'],
            [['fecha_creacion', 'fecha_edicion'], 'safe'],
            [['viaje_id'], 'exist', 'skipOnError' => true, 'targetClass' => Viajes::className(), 'targetAttribute' => ['viaje_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'viaje_id' => 'Viaje ID',
            'monto_aprovisionado' => 'Monto Aprovisionado',
            'monto_rendido' => 'Monto Rendido',
            'monto_saldo' => 'Monto Saldo',
            'fecha_creacion' => 'Fecha Creacion',
            'estado' => 'Estado',
            'estado_erp' => 'Estado Erp',
            'fecha_edicion' => 'Fecha Edicion',
        ];
    }

    /**
     * Gets query for [[Viaje]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViaje()
    {
        return $this->hasOne(Viajes::className(), ['id' => 'viaje_id']);
    }

    /**
     * Gets query for [[RendicionDetalles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRendicionDetalles()
    {
        return $this->hasMany(RendicionDetalle::className(), ['rendicion_id' => 'id']);
    }
}

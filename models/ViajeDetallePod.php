<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "viaje_detalle_pod".
 *
 * @property int $id
 * @property int $viaje_detalle_id
 * @property string $nombre_firma
 * @property string|null $rut_firma
 * @property string|null $empresa_firma
 * @property int $estatus_pod_id
 * @property string $fecha_creado
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 *
 * @property EstatusPod $estatusPod
 * @property ViajeDetalle $viajeDetalle
 */
class ViajeDetallePod extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'viaje_detalle_pod';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['viaje_detalle_id', 'nombre_firma', 'estatus_pod_id'], 'required'],
            [['viaje_detalle_id', 'estatus_pod_id'], 'default', 'value' => null],
            [['viaje_detalle_id', 'estatus_pod_id'], 'integer'],
            [['fecha_creado', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['nombre_firma', 'rut_firma', 'empresa_firma'], 'string', 'max' => 255],
            [['estatus_pod_id'], 'exist', 'skipOnError' => true, 'targetClass' => EstatusPod::className(), 'targetAttribute' => ['estatus_pod_id' => 'id']],
            [['viaje_detalle_id'], 'exist', 'skipOnError' => true, 'targetClass' => ViajeDetalle::className(), 'targetAttribute' => ['viaje_detalle_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'viaje_detalle_id' => 'Viaje Detalle ID',
            'nombre_firma' => 'Nombre Firma',
            'rut_firma' => 'Rut Firma',
            'empresa_firma' => 'Empresa Firma',
            'estatus_pod_id' => 'Estatus Pod ID',
            'fecha_creado' => 'Fecha Creado',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
        ];
    }

    /**
     * Gets query for [[EstatusPod]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEstatusPod()
    {
        return $this->hasOne(EstatusPod::className(), ['id' => 'estatus_pod_id']);
    }

    /**
     * Gets query for [[ViajeDetalle]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajeDetalle()
    {
        return $this->hasOne(ViajeDetalle::className(), ['id' => 'viaje_detalle_id']);
    }
}

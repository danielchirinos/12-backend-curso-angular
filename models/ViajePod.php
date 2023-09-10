<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "viaje_pod".
 *
 * @property int $id
 * @property int $viaje_id
 * @property int $estatus_pod_id
 * @property string $nombre_firma
 * @property string|null $rut_firma
 * @property string|null $empresa_firma
 * @property string $fecha_creado
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 *
 * @property EstatusPod $estatusPod
 * @property Viajes $viaje
 * @property ViajePodDetalle[] $viajePodDetalles
 */
class ViajePod extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'viaje_pod';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['viaje_id', 'estatus_pod_id', 'nombre_firma'], 'required'],
            [['viaje_id', 'estatus_pod_id'], 'default', 'value' => null],
            [['viaje_id', 'estatus_pod_id'], 'integer'],
            [['fecha_creado', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['nombre_firma', 'rut_firma', 'empresa_firma'], 'string', 'max' => 255],
            [['estatus_pod_id'], 'exist', 'skipOnError' => true, 'targetClass' => EstatusPod::className(), 'targetAttribute' => ['estatus_pod_id' => 'id']],
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
            'estatus_pod_id' => 'Estatus Pod ID',
            'nombre_firma' => 'Nombre Firma',
            'rut_firma' => 'Rut Firma',
            'empresa_firma' => 'Empresa Firma',
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
     * Gets query for [[Viaje]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViaje()
    {
        return $this->hasOne(Viajes::className(), ['id' => 'viaje_id']);
    }

    /**
     * Gets query for [[ViajePodDetalles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajePodDetalles()
    {
        return $this->hasMany(ViajePodDetalle::className(), ['viaje_pod_id' => 'id']);
    }
}

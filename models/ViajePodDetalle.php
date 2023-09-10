<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "viaje_pod_detalle".
 *
 * @property int $id
 * @property int $viaje_pod_id
 * @property int $viaje_detalle_id
 * @property string|null $foto
 * @property string|null $observacion
 * @property int|null $validado 0 enviado | 1 validado | 2 rechazado
 * @property int|null $tipo_documento_pod_id
 * @property int $estatus_pod_id
 *
 * @property EstatusPod $estatusPod
 * @property TipoDocumentoPod $tipoDocumentoPod
 * @property ViajeDetalle $viajeDetalle
 * @property ViajePod $viajePod
 */
class ViajePodDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'viaje_pod_detalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['viaje_pod_id', 'viaje_detalle_id', 'estatus_pod_id'], 'required'],
            [['viaje_pod_id', 'viaje_detalle_id', 'validado', 'tipo_documento_pod_id', 'estatus_pod_id'], 'default', 'value' => null],
            [['viaje_pod_id', 'viaje_detalle_id', 'validado', 'tipo_documento_pod_id', 'estatus_pod_id'], 'integer'],
            [['foto', 'observacion'], 'string'],
            [['estatus_pod_id'], 'exist', 'skipOnError' => true, 'targetClass' => EstatusPod::className(), 'targetAttribute' => ['estatus_pod_id' => 'id']],
            [['tipo_documento_pod_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoDocumentoPod::className(), 'targetAttribute' => ['tipo_documento_pod_id' => 'id']],
            [['viaje_detalle_id'], 'exist', 'skipOnError' => true, 'targetClass' => ViajeDetalle::className(), 'targetAttribute' => ['viaje_detalle_id' => 'id']],
            [['viaje_pod_id'], 'exist', 'skipOnError' => true, 'targetClass' => ViajePod::className(), 'targetAttribute' => ['viaje_pod_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'viaje_pod_id' => 'Viaje Pod ID',
            'viaje_detalle_id' => 'Viaje Detalle ID',
            'foto' => 'Foto',
            'observacion' => 'Observacion',
            'validado' => 'Validado',
            'tipo_documento_pod_id' => 'Tipo Documento Pod ID',
            'estatus_pod_id' => 'Estatus Pod ID',
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
     * Gets query for [[TipoDocumentoPod]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTipoDocumentoPod()
    {
        return $this->hasOne(TipoDocumentoPod::className(), ['id' => 'tipo_documento_pod_id']);
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

    /**
     * Gets query for [[ViajePod]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajePod()
    {
        return $this->hasOne(ViajePod::className(), ['id' => 'viaje_pod_id']);
    }
}

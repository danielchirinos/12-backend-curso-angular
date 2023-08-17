<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "viaje_detalle_pod_detalle".
 *
 * @property int $id
 * @property int $viaje_detalle_pod_id
 * @property string|null $foto
 * @property int|null $validado 0 para no validado | 1 para validado
 */
class ViajeDetallePodDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'viaje_detalle_pod_detalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['viaje_detalle_pod_id'], 'required'],
            [['viaje_detalle_pod_id', 'validado'], 'default', 'value' => null],
            [['viaje_detalle_pod_id', 'validado'], 'integer'],
            [['foto'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'viaje_detalle_pod_id' => 'Viaje Detalle Pod ID',
            'foto' => 'Foto',
            'validado' => 'Validado',
        ];
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "viajes_campos_opcionales".
 *
 * @property string $id
 * @property int $viaje_id
 * @property int $campo_opcional_id
 * @property string $valor
 *
 * @property CamposOpcionales $campoOpcional
 * @property Viajes $viaje
 */
class ViajesCamposOpcionales extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'viajes_campos_opcionales';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['viaje_id', 'campo_opcional_id', 'valor'], 'required'],
            [['viaje_id', 'campo_opcional_id'], 'default', 'value' => null],
            [['viaje_id', 'campo_opcional_id'], 'integer'],
            [['valor'], 'string', 'max' => 255],
            [['campo_opcional_id'], 'exist', 'skipOnError' => true, 'targetClass' => CamposOpcionales::className(), 'targetAttribute' => ['campo_opcional_id' => 'id']],
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
            'viaje_id' => 'Viaje',
            'campo_opcional_id' => 'Campo Opcional',
            'valor' => 'Valor',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCampoOpcional()
    {
        return $this->hasOne(CamposOpcionales::className(), ['id' => 'campo_opcional_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getViaje()
    {
        return $this->hasOne(Viajes::className(), ['id' => 'viaje_id']);
    }
}

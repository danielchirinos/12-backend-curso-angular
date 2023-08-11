<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contrato_documento".
 *
 * @property int $id
 * @property int $contrato_id
 * @property int $documento_id
 * @property int $obligatoriedad
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 *
 * @property Contrato $contrato
 * @property Documento $documento
 */
class ContratoDocumento extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contrato_documento';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['contrato_id', 'documento_id', 'obligatoriedad', 'fecha_creacion'], 'required'],
            [['contrato_id', 'documento_id', 'obligatoriedad'], 'default', 'value' => null],
            [['contrato_id', 'documento_id', 'obligatoriedad'], 'integer'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['contrato_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contrato::className(), 'targetAttribute' => ['contrato_id' => 'id']],
            [['documento_id'], 'exist', 'skipOnError' => true, 'targetClass' => Documento::className(), 'targetAttribute' => ['documento_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contrato_id' => 'Contrato ID',
            'documento_id' => 'Documento ID',
            'obligatoriedad' => 'Obligatoriedad',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
        ];
    }

    /**
     * Gets query for [[Contrato]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContrato()
    {
        return $this->hasOne(Contrato::className(), ['id' => 'contrato_id']);
    }

    /**
     * Gets query for [[Documento]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumento()
    {
        return $this->hasOne(Documento::className(), ['id' => 'documento_id']);
    }
}

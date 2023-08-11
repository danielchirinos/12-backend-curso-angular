<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_documentos".
 *
 * @property int $id
 * @property string|null $nro_interno
 * @property string $descripcion
 * @property string|null $tipo
 *
 * @property RendicionDetalle[] $rendicionDetalles
 */
class TipoDocumentos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipo_documentos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descripcion'], 'required'],
            [['nro_interno'], 'string', 'max' => 32],
            [['descripcion', 'tipo'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nro_interno' => 'Nro Interno',
            'descripcion' => 'Descripcion',
            'tipo' => 'Tipo',
        ];
    }

    /**
     * Gets query for [[RendicionDetalles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRendicionDetalles()
    {
        return $this->hasMany(RendicionDetalle::className(), ['tipo_documento_id' => 'id']);
    }
}

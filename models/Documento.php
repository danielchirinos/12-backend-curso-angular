<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "documento".
 *
 * @property int $id
 * @property string $documento
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 *
 * @property ContratoDocumento[] $contratoDocumentos
 * @property ViajeDocumento[] $viajeDocumentos
 */
class Documento extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'documento';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['documento', 'fecha_creacion'], 'required'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['documento'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'documento' => 'Documento',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
        ];
    }

    /**
     * Gets query for [[ContratoDocumentos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContratoDocumentos()
    {
        return $this->hasMany(ContratoDocumento::className(), ['documento_id' => 'id']);
    }

    /**
     * Gets query for [[ViajeDocumentos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajeDocumentos()
    {
        return $this->hasMany(ViajeDocumento::className(), ['documento_id' => 'id']);
    }
}

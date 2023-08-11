<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "documento_sii".
 *
 * @property int $id
 * @property string|null $nro_interno
 * @property string $descripcion
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 *
 * @property PrefacturaDocumentosReferencia[] $prefacturaDocumentosReferencias
 */
class DocumentoSii extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'documento_sii';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['descripcion', 'fecha_creacion'], 'required'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['nro_interno'], 'string', 'max' => 3],
            [['descripcion'], 'string', 'max' => 255],
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
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
        ];
    }

    /**
     * Gets query for [[PrefacturaDocumentosReferencias]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrefacturaDocumentosReferencias()
    {
        return $this->hasMany(PrefacturaDocumentosReferencia::className(), ['documento_sii_id' => 'id']);
    }
}

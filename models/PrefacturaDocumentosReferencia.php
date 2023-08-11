<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "prefactura_documentos_referencia".
 *
 * @property int $id
 * @property int $prefactura_id
 * @property int $documento_sii_id
 * @property string $nro_documento
 * @property string|null $razon
 * @property string|null $fecha_referencia
 * @property int|null $nro_linea
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 *
 * @property DocumentoSii $documentoSii
 * @property Prefactura $prefactura
 */
class PrefacturaDocumentosReferencia extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'prefactura_documentos_referencia';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['prefactura_id', 'documento_sii_id', 'nro_documento', 'fecha_creacion'], 'required'],
            [['prefactura_id', 'documento_sii_id', 'nro_linea'], 'default', 'value' => null],
            [['prefactura_id', 'documento_sii_id', 'nro_linea'], 'integer'],
            [['fecha_referencia', 'fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['nro_documento'], 'string', 'max' => 18],
            [['razon'], 'string', 'max' => 255],
            [['documento_sii_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentoSii::className(), 'targetAttribute' => ['documento_sii_id' => 'id']],
            [['prefactura_id'], 'exist', 'skipOnError' => true, 'targetClass' => Prefactura::className(), 'targetAttribute' => ['prefactura_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'prefactura_id' => 'Prefactura ID',
            'documento_sii_id' => 'Documento Sii ID',
            'nro_documento' => 'Nro Documento',
            'razon' => 'Razon',
            'fecha_referencia' => 'Fecha Referencia',
            'nro_linea' => 'Nro Linea',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
        ];
    }

    /**
     * Gets query for [[DocumentoSii]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentoSii()
    {
        return $this->hasOne(DocumentoSii::className(), ['id' => 'documento_sii_id']);
    }

    /**
     * Gets query for [[Prefactura]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrefactura()
    {
        return $this->hasOne(Prefactura::className(), ['id' => 'prefactura_id']);
    }
}

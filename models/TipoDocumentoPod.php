<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_documento_pod".
 *
 * @property int $id
 * @property string $nombre
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 *
 * @property ViajePodDetalle[] $viajePodDetalles
 */
class TipoDocumentoPod extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipo_documento_pod';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre'], 'required'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['nombre'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
        ];
    }

    /**
     * Gets query for [[ViajePodDetalles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajePodDetalles()
    {
        return $this->hasMany(ViajePodDetalle::className(), ['tipo_documento_pod_id' => 'id']);
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "zonas_categorias".
 *
 * @property string $id
 * @property string $nombre
 * @property string $tipo_zona_id
 * @property string $fecha_creacion
 * @property string $fecha_edicion
 * @property string $fecha_borrado
 *
 * @property TipoZonas $tipoZona
 */
class ZonasCategorias extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'zonas_categorias';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'tipo_zona_id', 'fecha_creacion'], 'required'],
            [['tipo_zona_id'], 'default', 'value' => null],
            [['tipo_zona_id'], 'integer'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['nombre'], 'string', 'max' => 255],
            [['tipo_zona_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoZonas::className(), 'targetAttribute' => ['tipo_zona_id' => 'id']],
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
            'tipo_zona_id' => 'Tipo Zona',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoZona()
    {
        return $this->hasOne(TipoZonas::className(), ['id' => 'tipo_zona_id']);
    }
}

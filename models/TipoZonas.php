<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_zonas".
 *
 * @property int $id
 * @property string $nombre
 * @property string $fecha_creacion
 * @property string $fecha_edicion
 * @property string $fecha_borrado
 *
 * @property Zonas[] $zonas
 * @property ZonasCategorias[] $zonasCategorias
 */
class TipoZonas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipo_zonas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'fecha_creacion'], 'required'],
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
     * @return \yii\db\ActiveQuery
     */
    public function getZonas()
    {
        return $this->hasMany(Zonas::className(), ['tipo_zona_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZonasCategorias()
    {
        return $this->hasMany(ZonasCategorias::className(), ['tipo_zona_id' => 'id']);
    }
}

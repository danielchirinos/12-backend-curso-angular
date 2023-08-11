<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rendicion_categoria".
 *
 * @property int $id
 * @property string $nombre
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 *
 * @property RendicionDetalle[] $rendicionDetalles
 * @property RendicionMotivo[] $rendicionMotivos
 */
class RendicionCategoria extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rendicion_categoria';
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
     * Gets query for [[RendicionDetalles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRendicionDetalles()
    {
        return $this->hasMany(RendicionDetalle::className(), ['categoria_rendicion_id' => 'id']);
    }

    /**
     * Gets query for [[RendicionMotivos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRendicionMotivos()
    {
        return $this->hasMany(RendicionMotivo::className(), ['rendicion_categoria_id' => 'id']);
    }
}

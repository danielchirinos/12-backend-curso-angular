<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_ingreso".
 *
 * @property int $id
 * @property string $nombre
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 *
 * @property RendicionDetalle[] $rendicionDetalles
 */
class TipoIngreso extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipo_ingreso';
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
        return $this->hasMany(RendicionDetalle::className(), ['tipo_ingreso_id' => 'id']);
    }
}

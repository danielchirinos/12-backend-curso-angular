<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "gasto".
 *
 * @property int $id
 * @property string $nombre
 * @property int $visible 0 para no aparecer en la ruta nuva, 1 para aparecer al crear una ruta nueva
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 *
 * @property RutaGasto[] $rutaGastos
 */
class Gasto extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gasto';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'visible', 'fecha_creacion'], 'required'],
            [['visible'], 'default', 'value' => null],
            [['visible'], 'integer'],
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
            'visible' => 'Visible',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
        ];
    }

    /**
     * Gets query for [[RutaGastos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRutaGastos()
    {
        return $this->hasMany(RutaGasto::className(), ['gasto_id' => 'id']);
    }
}

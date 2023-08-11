<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "unidad_medida".
 *
 * @property int $id
 * @property string $nombre
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 * @property int $fija 0 para cantidad / 1 para fija
 *
 * @property Carga[] $cargas
 */
class UnidadMedida extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'unidad_medida';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'fecha_creacion', 'fija'], 'required'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['fija'], 'default', 'value' => null],
            [['fija'], 'integer'],
            [['nombre'], 'string', 'max' => 255],
            [['nombre'], 'unique'],
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
            'fija' => 'Fija',
        ];
    }

    /**
     * Gets query for [[Cargas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCargas()
    {
        return $this->hasMany(Carga::className(), ['unidad_medida_id' => 'id']);
    }
}

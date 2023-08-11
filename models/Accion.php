<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "accion".
 *
 * @property int $id
 * @property string $nombre
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 *
 * @property ViajeAccion[] $viajeAccions
 * @property ZonaAccion[] $zonaAccions
 */
class Accion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'accion';
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
     * Gets query for [[ViajeAccions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajeAccions()
    {
        return $this->hasMany(ViajeAccion::className(), ['accion_id' => 'id']);
    }

    /**
     * Gets query for [[ZonaAccions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getZonaAccions()
    {
        return $this->hasMany(ZonaAccion::className(), ['accion_id' => 'id']);
    }
}

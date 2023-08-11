<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "subestatus_viaje".
 *
 * @property int $id
 * @property string $nombre
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 *
 * @property SubestatusViajeMotivos[] $subestatusViajeMotivos
 * @property ViajeNovedades[] $viajeNovedades
 * @property Viajes[] $viajes
 */
class SubestatusViaje extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subestatus_viaje';
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
     * Gets query for [[SubestatusViajeMotivos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubestatusViajeMotivos()
    {
        return $this->hasMany(SubestatusViajeMotivos::className(), ['subestatus_viaje_id' => 'id']);
    }

    /**
     * Gets query for [[ViajeNovedades]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajeNovedades()
    {
        return $this->hasMany(ViajeNovedades::className(), ['subestatus_viaje_id' => 'id']);
    }

    /**
     * Gets query for [[Viajes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajes()
    {
        return $this->hasMany(Viajes::className(), ['subestatus_viaje_id' => 'id']);
    }
}

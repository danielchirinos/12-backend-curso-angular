<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "estado_rampla".
 *
 * @property int $id
 * @property string $nombre
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 *
 * @property ViajeDetalleRampla[] $viajeDetalleRamplas
 */
class EstadoRampla extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'estado_rampla';
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
     * Gets query for [[ViajeDetalleRamplas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajeDetalleRamplas()
    {
        return $this->hasMany(ViajeDetalleRampla::className(), ['estado_rampla_id' => 'id']);
    }
}

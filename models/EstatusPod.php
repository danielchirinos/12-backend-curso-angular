<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "estatus_pod".
 *
 * @property int $id
 * @property string $nombre
 * @property string $color
 * @property string $fecha_creacion
 * @property string $fecha_edicion
 * @property string $fecha_borrado
 *
 * @property ViajeDetallePod[] $viajeDetallePods
 */
class EstatusPod extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'estatus_pod';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre'], 'required'],
            [['fecha_creacion', 'fecha_edicion'], 'safe'],
            [['nombre', 'color', 'fecha_borrado'], 'string', 'max' => 255],
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
            'color' => 'Color',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getViajeDetallePods()
    {
        return $this->hasMany(ViajeDetallePod::className(), ['estatus_pod_id' => 'id']);
    }
}

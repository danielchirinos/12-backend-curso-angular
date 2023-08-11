<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "campos_opcionales".
 *
 * @property int $id
 * @property string $nombre
 * @property string $fecha_creacion
 * @property string $fecha_edicion
 * @property string $fecha_borrado
 *
 * @property ViajesCamposOpcionales[] $viajesCamposOpcionales
 */
class CamposOpcionales extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'campos_opcionales';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre'], 'required'],
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
    public function getViajesCamposOpcionales()
    {
        return $this->hasMany(ViajesCamposOpcionales::className(), ['campo_opcional_id' => 'id']);
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "m_zonas".
 *
 * @property int $id
 * @property int $macrozona_id
 * @property string $nombre
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 *
 * @property Macrozonas $macrozona
 */
class MZonas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'm_zonas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['macrozona_id', 'nombre', 'fecha_creacion'], 'required'],
            [['macrozona_id'], 'default', 'value' => null],
            [['macrozona_id'], 'integer'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['nombre'], 'string', 'max' => 255],
            [['macrozona_id'], 'exist', 'skipOnError' => true, 'targetClass' => Macrozonas::className(), 'targetAttribute' => ['macrozona_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'macrozona_id' => 'Macrozona ID',
            'nombre' => 'Nombre',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
        ];
    }

    /**
     * Gets query for [[Macrozona]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMacrozona()
    {
        return $this->hasOne(Macrozonas::className(), ['id' => 'macrozona_id']);
    }
}

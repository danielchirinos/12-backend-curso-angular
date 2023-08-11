<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ruta_gasto".
 *
 * @property int $id
 * @property int $gasto_id
 * @property int $ruta_id
 * @property float $monto
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 *
 * @property Gasto $gasto
 * @property Ruta $ruta
 */
class RutaGasto extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ruta_gasto';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gasto_id', 'ruta_id', 'monto', 'fecha_creacion'], 'required'],
            [['gasto_id', 'ruta_id'], 'default', 'value' => null],
            [['gasto_id', 'ruta_id'], 'integer'],
            [['monto'], 'number'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['gasto_id'], 'exist', 'skipOnError' => true, 'targetClass' => Gasto::className(), 'targetAttribute' => ['gasto_id' => 'id']],
            [['ruta_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ruta::className(), 'targetAttribute' => ['ruta_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'gasto_id' => 'Gasto ID',
            'ruta_id' => 'Ruta ID',
            'monto' => 'Monto',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
        ];
    }

    /**
     * Gets query for [[Gasto]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGasto()
    {
        return $this->hasOne(Gasto::className(), ['id' => 'gasto_id']);
    }

    /**
     * Gets query for [[Ruta]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRuta()
    {
        return $this->hasOne(Ruta::className(), ['id' => 'ruta_id']);
    }
}

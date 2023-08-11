<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_vale".
 *
 * @property int $id
 * @property string $tipo
 * @property string|null $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 *
 * @property CajaVale[] $cajaVales
 */
class TipoVale extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipo_vale';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipo'], 'required'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['tipo'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tipo' => 'Tipo',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
        ];
    }

    /**
     * Gets query for [[CajaVales]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCajaVales()
    {
        return $this->hasMany(CajaVale::className(), ['tipo_vale_id' => 'id']);
    }
}

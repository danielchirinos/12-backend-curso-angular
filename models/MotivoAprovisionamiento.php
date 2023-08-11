<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "motivo_aprovisionamiento".
 *
 * @property int $id
 * @property string $motivo
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 * @property string|null $tipo 0 cualquier 1 para rendicion, no se muestran los de tipo 1 para editar o eliminar
 *
 * @property CajaVale[] $cajaVales
 */
class MotivoAprovisionamiento extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'motivo_aprovisionamiento';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['motivo', 'fecha_creacion'], 'required'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['motivo', 'tipo'], 'string', 'max' => 255],
            [['motivo'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'motivo' => 'Motivo',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
            'tipo' => 'Tipo',
        ];
    }

    /**
     * Gets query for [[CajaVales]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCajaVales()
    {
        return $this->hasMany(CajaVale::className(), ['motivo_aprovisionamiento_id' => 'id']);
    }
}

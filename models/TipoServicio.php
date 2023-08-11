<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_servicio".
 *
 * @property int $id
 * @property int $tipo_operacion_id
 * @property string $nombre
 *
 * @property TipoOperacion $tipoOperacion
 * @property Viajes[] $viajes
 */
class TipoServicio extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipo_servicio';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipo_operacion_id', 'nombre'], 'required'],
            [['tipo_operacion_id'], 'default', 'value' => null],
            [['tipo_operacion_id'], 'integer'],
            [['nombre'], 'string', 'max' => 255],
            [['tipo_operacion_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoOperacion::className(), 'targetAttribute' => ['tipo_operacion_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tipo_operacion_id' => 'Tipo Operacion ID',
            'nombre' => 'Nombre',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoOperacion()
    {
        return $this->hasOne(TipoOperacion::className(), ['id' => 'tipo_operacion_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getViajes()
    {
        return $this->hasMany(Viajes::className(), ['tipo_servicio_id' => 'id']);
    }
}

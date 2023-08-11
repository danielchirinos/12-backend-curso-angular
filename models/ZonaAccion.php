<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "zona_accion".
 *
 * @property int $id
 * @property int $zona_id
 * @property int $accion_id
 * @property int $orden
 * @property int|null $tiempo_recordatorio tiempo recordatorio para ejecutar boton
 * @property string|null $tiempo_ejecutar_accion tiempo para ejercutar la accion
 *
 * @property Accion $accion
 * @property Zonas $zona
 */
class ZonaAccion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'zona_accion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['zona_id', 'accion_id', 'orden'], 'required'],
            [['zona_id', 'accion_id', 'orden', 'tiempo_recordatorio'], 'default', 'value' => null],
            [['zona_id', 'accion_id', 'orden', 'tiempo_recordatorio'], 'integer'],
            [['tiempo_ejecutar_accion'], 'string', 'max' => 255],
            [['accion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Accion::className(), 'targetAttribute' => ['accion_id' => 'id']],
            [['zona_id'], 'exist', 'skipOnError' => true, 'targetClass' => Zonas::className(), 'targetAttribute' => ['zona_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'zona_id' => 'Zona ID',
            'accion_id' => 'Accion ID',
            'orden' => 'Orden',
            'tiempo_recordatorio' => 'Tiempo Recordatorio',
            'tiempo_ejecutar_accion' => 'Tiempo Ejecutar Accion',
        ];
    }

    /**
     * Gets query for [[Accion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccion()
    {
        return $this->hasOne(Accion::className(), ['id' => 'accion_id']);
    }

    /**
     * Gets query for [[Zona]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getZona()
    {
        return $this->hasOne(Zonas::className(), ['id' => 'zona_id']);
    }
}

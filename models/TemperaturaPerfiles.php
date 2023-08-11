<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "temperatura_perfiles".
 *
 * @property int $id
 * @property string $nombre
 * @property float $min_t1
 * @property float $max_t1
 * @property int $tiempo_tolerancia_min_t1
 * @property int $tiempo_tolerancia_max_t1
 * @property float $min_t2
 * @property float $max_t2
 * @property int $tiempo_tolerancia_min_t2
 * @property int $tiempo_tolerancia_max_t2
 * @property float $min_t3
 * @property float $max_t3
 * @property int $tiempo_tolerancia_min_t3
 * @property int $tiempo_tolerancia_max_t3
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 *
 * @property Viajes[] $viajes
 */
class TemperaturaPerfiles extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'temperatura_perfiles';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'min_t1', 'max_t1', 'tiempo_tolerancia_min_t1', 'tiempo_tolerancia_max_t1', 'min_t2', 'max_t2', 'tiempo_tolerancia_min_t2', 'tiempo_tolerancia_max_t2', 'min_t3', 'max_t3', 'tiempo_tolerancia_min_t3', 'tiempo_tolerancia_max_t3', 'fecha_creacion'], 'required'],
            [['min_t1', 'max_t1', 'min_t2', 'max_t2', 'min_t3', 'max_t3'], 'number'],
            [['tiempo_tolerancia_min_t1', 'tiempo_tolerancia_max_t1', 'tiempo_tolerancia_min_t2', 'tiempo_tolerancia_max_t2', 'tiempo_tolerancia_min_t3', 'tiempo_tolerancia_max_t3'], 'default', 'value' => null],
            [['tiempo_tolerancia_min_t1', 'tiempo_tolerancia_max_t1', 'tiempo_tolerancia_min_t2', 'tiempo_tolerancia_max_t2', 'tiempo_tolerancia_min_t3', 'tiempo_tolerancia_max_t3'], 'integer'],
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
            'min_t1' => 'Min T1',
            'max_t1' => 'Max T1',
            'tiempo_tolerancia_min_t1' => 'Tiempo Tolerancia Min T1',
            'tiempo_tolerancia_max_t1' => 'Tiempo Tolerancia Max T1',
            'min_t2' => 'Min T2',
            'max_t2' => 'Max T2',
            'tiempo_tolerancia_min_t2' => 'Tiempo Tolerancia Min T2',
            'tiempo_tolerancia_max_t2' => 'Tiempo Tolerancia Max T2',
            'min_t3' => 'Min T3',
            'max_t3' => 'Max T3',
            'tiempo_tolerancia_min_t3' => 'Tiempo Tolerancia Min T3',
            'tiempo_tolerancia_max_t3' => 'Tiempo Tolerancia Max T3',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
        ];
    }

    /**
     * Gets query for [[Viajes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajes()
    {
        return $this->hasMany(Viajes::className(), ['temperatura_perfil_id' => 'id']);
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "eventos".
 *
 * @property string $id
 * @property string $ev_id
 * @property string $vh_id
 * @property string $zn_id
 * @property int $tev_id
 * @property string $ev_fecha
 * @property string $hp_latitud
 * @property string $hp_longitud
 * @property string $horometro
 * @property string $odometro
 */
class Eventos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'eventos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ev_id', 'vh_id', 'zn_id', 'tev_id'], 'default', 'value' => null],
            [['ev_id', 'vh_id', 'zn_id', 'tev_id'], 'integer'],
            [['ev_fecha'], 'safe'],
            [['hp_latitud', 'hp_longitud'], 'string', 'max' => 30],
            [['horometro', 'odometro'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ev_id' => 'Ev ID',
            'vh_id' => 'Vh ID',
            'zn_id' => 'Zn ID',
            'tev_id' => 'Tev ID',
            'ev_fecha' => 'Ev Fecha',
            'hp_latitud' => 'Hp Latitud',
            'hp_longitud' => 'Hp Longitud',
            'horometro' => 'Horometro',
            'odometro' => 'Odometro',
        ];
    }
}

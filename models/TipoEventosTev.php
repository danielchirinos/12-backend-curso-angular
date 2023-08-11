<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_eventos_tev".
 *
 * @property int $tev_id
 * @property string $tev_desc
 * @property string $sync_stat
 * @property string $tev_muestra
 *
 * @property EventosRs[] $eventosRs
 */
class TipoEventosTev extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipo_eventos_tev';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tev_id', 'tev_desc', 'sync_stat', 'tev_muestra'], 'required'],
            [['tev_id'], 'default', 'value' => null],
            [['tev_id'], 'integer'],
            [['tev_desc', 'sync_stat', 'tev_muestra'], 'string', 'max' => 255],
            [['tev_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'tev_id' => 'Tev ID',
            'tev_desc' => 'Tev Desc',
            'sync_stat' => 'Sync Stat',
            'tev_muestra' => 'Tev Muestra',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventosRs()
    {
        return $this->hasMany(EventosRs::className(), ['tev_id' => 'tev_id']);
    }
}

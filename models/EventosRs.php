<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "eventos_rs".
 *
 * @property int $id
 * @property int $ev_id
 * @property int $tev_id
 * @property int $us_id
 * @property int $zn_id
 * @property string $ev_text
 * @property string $ev_fecha
 * @property string $vh_patente
 * @property int $eu_id
 * @property int $teu_id
 * @property int $vh_id
 * @property string $persona_contactada
 * @property string $observacion
 * @property string $estado_evento 0 sin antender, 1 atendido
 * @property int $id_ruta_segura
 *
 * @property TipoEventosTev $tev
 * @property Vehiculos $vh
 * @property Zonas $zn
 */
class EventosRs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'eventos_rs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ev_id', 'tev_id', 'us_id', 'zn_id', 'eu_id', 'teu_id', 'vh_id', 'id_ruta_segura'], 'default', 'value' => null],
            [['ev_id', 'tev_id', 'us_id', 'zn_id', 'eu_id', 'teu_id', 'vh_id', 'id_ruta_segura'], 'integer'],
            [['ev_text', 'ev_fecha', 'vh_patente', 'persona_contactada', 'observacion', 'estado_evento'], 'string', 'max' => 255],
            [['tev_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoEventosTev::className(), 'targetAttribute' => ['tev_id' => 'tev_id']],
            [['vh_id'], 'exist', 'skipOnError' => true, 'targetClass' => Vehiculos::className(), 'targetAttribute' => ['vh_id' => 'id']],
            [['zn_id'], 'exist', 'skipOnError' => true, 'targetClass' => Zonas::className(), 'targetAttribute' => ['zn_id' => 'id']],
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
            'tev_id' => 'Tev ID',
            'us_id' => 'Us ID',
            'zn_id' => 'Zn ID',
            'ev_text' => 'Ev Text',
            'ev_fecha' => 'Ev Fecha',
            'vh_patente' => 'Vh Patente',
            'eu_id' => 'Eu ID',
            'teu_id' => 'Teu ID',
            'vh_id' => 'Vh ID',
            'persona_contactada' => 'Persona Contactada',
            'observacion' => 'Observacion',
            'estado_evento' => 'Estado Evento',
            'id_ruta_segura' => 'Id Ruta Segura',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTev()
    {
        return $this->hasOne(TipoEventosTev::className(), ['tev_id' => 'tev_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVh()
    {
        return $this->hasOne(Vehiculos::className(), ['id' => 'vh_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZn()
    {
        return $this->hasOne(Zonas::className(), ['id' => 'zn_id']);
    }
}

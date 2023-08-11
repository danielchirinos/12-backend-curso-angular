<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "estados_prefactura_proforma".
 *
 * @property int $id
 * @property string $estado
 *
 * @property Prefactura[] $prefacturas
 * @property Proforma[] $proformas
 */
class EstadosPrefacturaProforma extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'estados_prefactura_proforma';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['estado'], 'required'],
            [['estado'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'estado' => 'Estado',
        ];
    }

    /**
     * Gets query for [[Prefacturas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrefacturas()
    {
        return $this->hasMany(Prefactura::className(), ['estado_prefactura_id' => 'id']);
    }

    /**
     * Gets query for [[Proformas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProformas()
    {
        return $this->hasMany(Proforma::className(), ['estado_proforma_id' => 'id']);
    }
}

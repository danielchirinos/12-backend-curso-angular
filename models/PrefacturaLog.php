<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "prefactura_log".
 *
 * @property int $id
 * @property int $prefactura_id
 * @property string|null $request
 * @property string|null $response
 * @property int $estado 0 error, 1 para ok
 * @property string|null $descripcion si existe un error se llena en este campo la descripcion
 * @property string $fecha_creacion
 * @property int $nota_credito 0 para prefactura, 1 para nota de creadito
 *
 * @property Prefactura $prefactura
 */
class PrefacturaLog extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'prefactura_log';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['prefactura_id', 'estado', 'fecha_creacion'], 'required'],
            [['prefactura_id', 'estado', 'nota_credito'], 'default', 'value' => null],
            [['prefactura_id', 'estado', 'nota_credito'], 'integer'],
            [['request', 'response', 'descripcion'], 'string'],
            [['fecha_creacion'], 'safe'],
            [['prefactura_id'], 'exist', 'skipOnError' => true, 'targetClass' => Prefactura::className(), 'targetAttribute' => ['prefactura_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'prefactura_id' => 'Prefactura ID',
            'request' => 'Request',
            'response' => 'Response',
            'estado' => 'Estado',
            'descripcion' => 'Descripcion',
            'fecha_creacion' => 'Fecha Creacion',
            'nota_credito' => 'Nota Credito',
        ];
    }

    /**
     * Gets query for [[Prefactura]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrefactura()
    {
        return $this->hasOne(Prefactura::className(), ['id' => 'prefactura_id']);
    }
}

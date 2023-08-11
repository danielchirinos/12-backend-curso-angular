<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "transportistas_contactos".
 *
 * @property string $id
 * @property string $transportista_id Transportista
 * @property string $nombre
 * @property string $apellido
 * @property string $cargo
 * @property string $email
 * @property string $telefono
 * @property string $celular
 * @property string $fecha_creacion
 * @property string $fecha_edicion
 * @property string $fecha_borrado
 *
 * @property Transportistas $transportista
 */
class TransportistasContactos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transportistas_contactos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['transportista_id', 'nombre', 'apellido', 'cargo', 'email', 'telefono'], 'required'],
            [['transportista_id'], 'default', 'value' => null],
            [['transportista_id'], 'integer'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['nombre', 'apellido', 'cargo', 'email', 'telefono', 'celular'], 'string', 'max' => 255],
            [['transportista_id'], 'exist', 'skipOnError' => true, 'targetClass' => Transportistas::className(), 'targetAttribute' => ['transportista_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'transportista_id' => 'Transportista',
            'nombre' => 'Nombre',
            'apellido' => 'Apellido',
            'cargo' => 'Cargo',
            'email' => 'Email',
            'telefono' => 'Telefono',
            'celular' => 'Celular',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransportista()
    {
        return $this->hasOne(Transportistas::className(), ['id' => 'transportista_id']);
    }
}

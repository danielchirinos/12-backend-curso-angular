<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "clientes_contactos".
 *
 * @property int $id
 * @property int $cliente_id Cliente
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
 * @property Clientes $cliente
 */
class ClientesContactos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'clientes_contactos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cliente_id', 'nombre', 'apellido', 'cargo', 'email', 'telefono'], 'required'],
            [['cliente_id'], 'default', 'value' => null],
            [['cliente_id'], 'integer'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['nombre', 'apellido', 'cargo', 'email', 'telefono', 'celular'], 'string', 'max' => 255],
            [['cliente_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clientes::className(), 'targetAttribute' => ['cliente_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'cliente_id' => 'Cliente',
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
    public function getCliente()
    {
        return $this->hasOne(Clientes::className(), ['id' => 'cliente_id']);
    }
}

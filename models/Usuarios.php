<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "usuarios".
 *
 * @property int $id
 * @property string $nombre
 * @property string $apellido
 * @property string $email
 * @property string $usuario
 * @property string $clave
 * @property int $tipo_usuario_id
 * @property string $fecha_creacion
 * @property string $fecha_edicion
 * @property string $fecha_borrado
 * @property string $clientes_usuario clientes que puede ver el usuario
 *
 * @property DocumentosDrive[] $documentosDrives
 * @property TipoUsuario $tipoUsuario
 */
class Usuarios extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuarios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'apellido', 'email', 'usuario', 'clave', 'tipo_usuario_id'], 'required'],
            [['tipo_usuario_id'], 'default', 'value' => null],
            [['tipo_usuario_id'], 'integer'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['clientes_usuario'], 'string'],
            [['nombre', 'apellido', 'email', 'usuario', 'clave'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [['tipo_usuario_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoUsuario::className(), 'targetAttribute' => ['tipo_usuario_id' => 'id']],
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
            'apellido' => 'Apellido',
            'email' => 'Email',
            'usuario' => 'Usuario',
            'clave' => 'Clave',
            'tipo_usuario_id' => 'Tipo Usuario ID',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
            'clientes_usuario' => 'Clientes Usuario',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentosDrives()
    {
        return $this->hasMany(DocumentosDrive::className(), ['id_usuario' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoUsuario()
    {
        return $this->hasOne(TipoUsuario::className(), ['id' => 'tipo_usuario_id']);
    }
}

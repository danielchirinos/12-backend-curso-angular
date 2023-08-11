<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "auditoria".
 *
 * @property int $id
 * @property int $usuario_id
 * @property int $permiso_id
 * @property string $descripcion
 * @property string $fecha_creacion
 *
 * @property Permisos $permiso
 * @property Usuarios $usuario
 */
class Auditoria extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auditoria';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usuario_id', 'permiso_id', 'descripcion', 'fecha_creacion'], 'required'],
            [['usuario_id', 'permiso_id'], 'default', 'value' => null],
            [['usuario_id', 'permiso_id'], 'integer'],
            [['fecha_creacion'], 'safe'],
            [['descripcion'], 'string', 'max' => 255],
            [['permiso_id'], 'exist', 'skipOnError' => true, 'targetClass' => Permisos::className(), 'targetAttribute' => ['permiso_id' => 'id']],
            [['usuario_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['usuario_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'usuario_id' => 'Usuario ID',
            'permiso_id' => 'Permiso ID',
            'descripcion' => 'Descripcion',
            'fecha_creacion' => 'Fecha Creacion',
        ];
    }

    /**
     * Gets query for [[Permiso]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPermiso()
    {
        return $this->hasOne(Permisos::className(), ['id' => 'permiso_id']);
    }

    /**
     * Gets query for [[Usuario]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'usuario_id']);
    }
}

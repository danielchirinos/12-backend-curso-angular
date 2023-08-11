<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "permisos".
 *
 * @property int $id
 * @property string $nombre
 * @property string $nombre_submodulo
 * @property string|null $nombre_modulo
 *
 * @property Auditoria[] $auditorias
 * @property PermisosTipoUsuario[] $permisosTipoUsuarios
 * @property PermisosUsuario[] $permisosUsuarios
 */
class Permisos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'permisos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'nombre_submodulo'], 'required'],
            [['nombre', 'nombre_submodulo', 'nombre_modulo'], 'string', 'max' => 255],
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
            'nombre_submodulo' => 'Nombre Submodulo',
            'nombre_modulo' => 'Nombre Modulo',
        ];
    }

    /**
     * Gets query for [[Auditorias]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuditorias()
    {
        return $this->hasMany(Auditoria::className(), ['permiso_id' => 'id']);
    }

    /**
     * Gets query for [[PermisosTipoUsuarios]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPermisosTipoUsuarios()
    {
        return $this->hasMany(PermisosTipoUsuario::className(), ['permiso_id' => 'id']);
    }

    /**
     * Gets query for [[PermisosUsuarios]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPermisosUsuarios()
    {
        return $this->hasMany(PermisosUsuario::className(), ['permiso_id' => 'id']);
    }
}

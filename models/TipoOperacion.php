<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tipo_operacion".
 *
 * @property int $id
 * @property string $nombre
 * @property string $fecha_creacion
 * @property string $fecha_edicion
 * @property string $fecha_borrado
 *
 * @property ConfiguracionUsuario[] $configuracionUsuarios
 * @property TipoServicio[] $tipoServicios
 */
class TipoOperacion extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tipo_operacion';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['nombre'], 'string', 'max' => 255],
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
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getConfiguracionUsuarios()
    {
        return $this->hasMany(ConfiguracionUsuario::className(), ['tipo_operacion_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoServicios()
    {
        return $this->hasMany(TipoServicio::className(), ['tipo_operacion_id' => 'id']);
    }
}

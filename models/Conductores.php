<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "conductores".
 *
 * @property int $id
 * @property int $transportista_id Transportista
 * @property string $nombre
 * @property string $apellido
 * @property string $documento RUT
 * @property string $telefono
 * @property string $email
 * @property string $licencia
 * @property string $vencimiento_licencia Vencimiento de Licencia
 * @property int $estado_conductor Estado
 * @property string $foto
 * @property string $usuario
 * @property string $clave
 * @property string $fecha_creacion
 * @property string $fecha_edicion
 * @property string $fecha_borrado
 *
 * @property Transportistas $transportista
 * @property Viajes[] $viajes
 */
class Conductores extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'conductores';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['transportista_id', 'nombre', 'apellido', 'usuario', 'clave'], 'required'],
            [['nombre'], 'string','min'=>4],
            [['apellido'], 'string','min'=>2],
            [['transportista_id', 'estado_conductor'], 'default', 'value' => null],
            [['transportista_id', 'estado_conductor'], 'integer'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['documento', 'email', 'licencia', 'vencimiento_licencia', 'foto', 'usuario', 'clave'], 'string', 'max' => 255],
            [['telefono'], 'string', 'max' => 15],
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
            'documento' => 'Rut',
            'telefono' => 'Telefono',
            'email' => 'Email',
            'licencia' => 'Licencia',
            'vencimiento_licencia' => 'Vencimiento de Licencia',
            'estado_conductor' => 'Estado',
            'foto' => 'Foto',
            'usuario' => 'Usuario',
            'clave' => 'Clave',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getViajes()
    {
        return $this->hasMany(Viajes::className(), ['conductor_id' => 'id']);
    }
}

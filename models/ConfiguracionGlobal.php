<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "configuracion_global".
 *
 * @property int $id
 * @property string $usuario_skyview
 * @property string $clave_skyview
 * @property string $fecha_creacion
 * @property string $fecha_edicion
 * @property string $fecha_borrado
 * @property string $tiempo_previo tolerancia
 * @property string $identificador_viaje nombre que mostrara el identifacador el viaje, por defecto ID
 * @property int $buscador_viaje 1 para id / 2 para nro de viaje
 * @property string $validador_fecha 1 para fecha hora entrada origen
 2 para fecha hora salida origen
 3 para fecha hora entrada destino
 4 para fecha hora salida destino
 5 solo origen
 6 solo destino
 7 todos
 * @property int $id_usuario_skyview
 * @property string $tiempo_previo_semaforo
 */
class ConfiguracionGlobal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'configuracion_global';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usuario_skyview', 'clave_skyview'], 'required'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado', 'tiempo_previo', 'tiempo_previo_semaforo'], 'safe'],
            [['buscador_viaje', 'id_usuario_skyview'], 'default', 'value' => null],
            [['buscador_viaje', 'id_usuario_skyview'], 'integer'],
            [['usuario_skyview', 'clave_skyview', 'identificador_viaje', 'validador_fecha'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'usuario_skyview' => 'Usuario Skyview',
            'clave_skyview' => 'Clave Skyview',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
            'tiempo_previo' => 'Tiempo Previo',
            'identificador_viaje' => 'Identificador Viaje',
            'buscador_viaje' => 'Buscador Viaje',
            'validador_fecha' => 'Validador Fecha',
            'id_usuario_skyview' => 'Id Usuario Skyview',
            'tiempo_previo_semaforo' => 'Tiempo Previo Semaforo',
        ];
    }
}

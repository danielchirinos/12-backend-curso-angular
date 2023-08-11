<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "configuracion_usuario".
 *
 * @property int $id
 * @property int $tipo_operacion_id Tipo Servicio
 * @property string $campos_opcionales
 * @property string $tipo_usuario_id
 * @property string $orden_campos_opcionales
 * @property string $visibilidad_campos_opcionales
 * @property string $orden_campos_viaje
 *
 * @property TipoOperacion $tipoOperacion
 */
class ConfiguracionUsuario extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'configuracion_usuario';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipo_operacion_id', 'tipo_usuario_id'], 'required'],
            [['tipo_operacion_id'], 'default', 'value' => null],
            [['tipo_operacion_id'], 'integer'],
            //[['campos_opcionales', 'tipo_usuario_id', 'orden_campos_opcionales', 'visibilidad_campos_opcionales', 'orden_campos_viaje'], 'string'],
            [[ 'orden_campos_opcionales' ], 'string'],
            [['tipo_operacion_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoOperacion::className(), 'targetAttribute' => ['tipo_operacion_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tipo_operacion_id' => 'Tipo Operacion ID',
            'campos_opcionales' => 'Campos Opcionales',
            'tipo_usuario_id' => 'Tipo Usuario ID',
            'orden_campos_opcionales' => 'Orden Campos Opcionales',
            'visibilidad_campos_opcionales' => 'Visibilidad Campos Opcionales',
            'orden_campos_viaje' => 'Orden Campos Viaje',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoOperacion()
    {
        return $this->hasOne(TipoOperacion::className(), ['id' => 'tipo_operacion_id']);
    }
}

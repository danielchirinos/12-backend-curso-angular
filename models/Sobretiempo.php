<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sobretiempo".
 *
 * @property int $id
 * @property int $zona_id
 * @property int $cliente_id
 * @property string $tiempo_maximo_permanencia_ontime
 * @property string $tiempo_maximo_permamencia_atrasado
 * @property string $tiempo_maximo_permanencia_libre
 * @property string $tolerancia_ontime
 * @property string $tipo_de_cobro
 * @property double $monto_cobrar
 * @property string $fecha_creacion
 * @property string $fecha_edicion
 * @property string $fecha_borrado
 * @property int $activo
 *
 * @property Clientes $cliente
 * @property Zonas $zona
 */
class Sobretiempo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sobretiempo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['zona_id', 'cliente_id', 'tipo_de_cobro', 'monto_cobrar', 'fecha_creacion'], 'required'],
            [['zona_id', 'cliente_id', 'activo'], 'default', 'value' => null],
            [['zona_id', 'cliente_id', 'activo'], 'integer'],
            [['tiempo_maximo_permanencia_ontime', 'tiempo_maximo_permamencia_atrasado', 'tiempo_maximo_permanencia_libre', 'tolerancia_ontime', 'fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['monto_cobrar'], 'number'],
            [['tipo_de_cobro'], 'string', 'max' => 40],
            [['cliente_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clientes::className(), 'targetAttribute' => ['cliente_id' => 'id']],
            [['zona_id'], 'exist', 'skipOnError' => true, 'targetClass' => Zonas::className(), 'targetAttribute' => ['zona_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'zona_id' => 'Zona ID',
            'cliente_id' => 'Cliente ID',
            'tiempo_maximo_permanencia_ontime' => 'Tiempo Maximo Permanencia Ontime',
            'tiempo_maximo_permamencia_atrasado' => 'Tiempo Maximo Permamencia Atrasado',
            'tiempo_maximo_permanencia_libre' => 'Tiempo Maximo Permanencia Libre',
            'tolerancia_ontime' => 'Tolerancia Ontime',
            'tipo_de_cobro' => 'Tipo De Cobro',
            'monto_cobrar' => 'Monto Cobrar',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
            'activo' => 'Activo',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCliente()
    {
        return $this->hasOne(Clientes::className(), ['id' => 'cliente_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZona()
    {
        return $this->hasOne(Zonas::className(), ['id' => 'zona_id']);
    }
}

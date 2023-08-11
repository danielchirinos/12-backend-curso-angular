<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "hoja_ruta".
 *
 * @property int $id
 * @property string $nro_hr
 * @property int $unidad_negocio_id
 * @property int $tipo_operacion_id
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 *
 * @property TipoOperacion $tipoOperacion
 * @property UnidadNegocio $unidadNegocio
 * @property Viajes[] $viajes
 */
class HojaRuta extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hoja_ruta';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nro_hr', 'unidad_negocio_id', 'tipo_operacion_id', 'fecha_creacion'], 'required'],
            [['unidad_negocio_id', 'tipo_operacion_id'], 'default', 'value' => null],
            [['unidad_negocio_id', 'tipo_operacion_id'], 'integer'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['nro_hr'], 'string', 'max' => 255],
            [['tipo_operacion_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoOperacion::className(), 'targetAttribute' => ['tipo_operacion_id' => 'id']],
            [['unidad_negocio_id'], 'exist', 'skipOnError' => true, 'targetClass' => UnidadNegocio::className(), 'targetAttribute' => ['unidad_negocio_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nro_hr' => 'Nro Hr',
            'unidad_negocio_id' => 'Unidad Negocio ID',
            'tipo_operacion_id' => 'Tipo Operacion ID',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
        ];
    }

    /**
     * Gets query for [[TipoOperacion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTipoOperacion()
    {
        return $this->hasOne(TipoOperacion::className(), ['id' => 'tipo_operacion_id']);
    }

    /**
     * Gets query for [[UnidadNegocio]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUnidadNegocio()
    {
        return $this->hasOne(UnidadNegocio::className(), ['id' => 'unidad_negocio_id']);
    }

    /**
     * Gets query for [[Viajes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajes()
    {
        return $this->hasMany(Viajes::className(), ['hoja_ruta_id' => 'id']);
    }
}

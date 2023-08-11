<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "carga".
 *
 * @property int $id
 * @property int|null $unidad_medida_id
 * @property int|null $categoria_carga_id
 * @property int|null $configuracion_carga_id
 * @property int|null $contenido_carga_id
 * @property int|null $temperatura_carga_id
 * @property int|null $comercio_id
 * @property int|null $tipo_rampla_id
 * @property string|null $codigo_carga
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 *
 * @property CategoriaCarga $categoriaCarga
 * @property Comercio $comercio
 * @property ConfiguracionCarga $configuracionCarga
 * @property ContenidoCarga $contenidoCarga
 * @property TemperaturaCarga $temperaturaCarga
 * @property TipoRampla $tipoRampla
 * @property UnidadMedida $unidadMedida
 * @property ContratoRuta[] $contratoRutas
 * @property Viajes[] $viajes
 */
class Carga extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'carga';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['unidad_medida_id', 'categoria_carga_id', 'configuracion_carga_id', 'contenido_carga_id', 'temperatura_carga_id', 'comercio_id', 'tipo_rampla_id'], 'default', 'value' => null],
            [['unidad_medida_id', 'categoria_carga_id', 'configuracion_carga_id', 'contenido_carga_id', 'temperatura_carga_id', 'comercio_id', 'tipo_rampla_id'], 'integer'],
            [['fecha_creacion'], 'required'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['codigo_carga'], 'string', 'max' => 255],
            [['codigo_carga'], 'unique'],
            [['categoria_carga_id'], 'exist', 'skipOnError' => true, 'targetClass' => CategoriaCarga::className(), 'targetAttribute' => ['categoria_carga_id' => 'id']],
            [['comercio_id'], 'exist', 'skipOnError' => true, 'targetClass' => Comercio::className(), 'targetAttribute' => ['comercio_id' => 'id']],
            [['configuracion_carga_id'], 'exist', 'skipOnError' => true, 'targetClass' => ConfiguracionCarga::className(), 'targetAttribute' => ['configuracion_carga_id' => 'id']],
            [['contenido_carga_id'], 'exist', 'skipOnError' => true, 'targetClass' => ContenidoCarga::className(), 'targetAttribute' => ['contenido_carga_id' => 'id']],
            [['temperatura_carga_id'], 'exist', 'skipOnError' => true, 'targetClass' => TemperaturaCarga::className(), 'targetAttribute' => ['temperatura_carga_id' => 'id']],
            [['tipo_rampla_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoRampla::className(), 'targetAttribute' => ['tipo_rampla_id' => 'id']],
            [['unidad_medida_id'], 'exist', 'skipOnError' => true, 'targetClass' => UnidadMedida::className(), 'targetAttribute' => ['unidad_medida_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'unidad_medida_id' => 'Unidad Medida ID',
            'categoria_carga_id' => 'Categoria Carga ID',
            'configuracion_carga_id' => 'Configuracion Carga ID',
            'contenido_carga_id' => 'Contenido Carga ID',
            'temperatura_carga_id' => 'Temperatura Carga ID',
            'comercio_id' => 'Comercio ID',
            'tipo_rampla_id' => 'Tipo Rampla ID',
            'codigo_carga' => 'Codigo Carga',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
        ];
    }

    /**
     * Gets query for [[CategoriaCarga]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategoriaCarga()
    {
        return $this->hasOne(CategoriaCarga::className(), ['id' => 'categoria_carga_id']);
    }

    /**
     * Gets query for [[Comercio]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComercio()
    {
        return $this->hasOne(Comercio::className(), ['id' => 'comercio_id']);
    }

    /**
     * Gets query for [[ConfiguracionCarga]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getConfiguracionCarga()
    {
        return $this->hasOne(ConfiguracionCarga::className(), ['id' => 'configuracion_carga_id']);
    }

    /**
     * Gets query for [[ContenidoCarga]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContenidoCarga()
    {
        return $this->hasOne(ContenidoCarga::className(), ['id' => 'contenido_carga_id']);
    }

    /**
     * Gets query for [[TemperaturaCarga]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTemperaturaCarga()
    {
        return $this->hasOne(TemperaturaCarga::className(), ['id' => 'temperatura_carga_id']);
    }

    /**
     * Gets query for [[TipoRampla]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTipoRampla()
    {
        return $this->hasOne(TipoRampla::className(), ['id' => 'tipo_rampla_id']);
    }

    /**
     * Gets query for [[UnidadMedida]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUnidadMedida()
    {
        return $this->hasOne(UnidadMedida::className(), ['id' => 'unidad_medida_id']);
    }

    /**
     * Gets query for [[ContratoRutas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContratoRutas()
    {
        return $this->hasMany(ContratoRuta::className(), ['carga_id' => 'id']);
    }

    /**
     * Gets query for [[Viajes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajes()
    {
        return $this->hasMany(Viajes::className(), ['codigo_carga' => 'codigo_carga']);
    }
}

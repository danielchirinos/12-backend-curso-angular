<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "zonas".
 *
 * @property int $id
 * @property int $tipo_zona_id Tipo Zona
 * @property string $nombre
 * @property string $direccion
 * @property int $zona_categoria_id Categoria
 * @property string $geom
 * @property string $max_tiempo_permanencia Máximo Tiempo Permanencia
 * @property string $valor_minuto $ x Minuto Adicional
 * @property string $fecha_creacion
 * @property string $fecha_edicion
 * @property string $fecha_borrado
 * @property string $centro
 *
 * @property EventosRs[] $eventosRs
 * @property ViajeDetalle[] $viajeDetalles
 * @property TipoZonas $tipoZona
 * @property ZonasCategorias $zonaCategoria
 */
class Zonas extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'zonas';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tipo_zona_id', 'nombre', 'zona_categoria_id', 'geom'], 'required'],
            [['tipo_zona_id', 'zona_categoria_id'], 'default', 'value' => null],
            [['tipo_zona_id', 'zona_categoria_id'], 'integer'],
            [['geom'], 'string'],
            [['max_tiempo_permanencia', 'fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['nombre', 'direccion', 'valor_minuto', 'centro'], 'string', 'max' => 255],
            [['tipo_zona_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoZonas::className(), 'targetAttribute' => ['tipo_zona_id' => 'id']],
            [['zona_categoria_id'], 'exist', 'skipOnError' => true, 'targetClass' => ZonasCategorias::className(), 'targetAttribute' => ['zona_categoria_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tipo_zona_id' => 'Tipo Zona',
            'nombre' => 'Nombre',
            'direccion' => 'Direccion',
            'zona_categoria_id' => 'Zona Categoria',
            'geom' => 'Geom',
            'max_tiempo_permanencia' => 'Max Tiempo Permanencia',
            'valor_minuto' => 'Valor Minuto',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
            'centro' => 'Centro',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventosRs()
    {
        return $this->hasMany(EventosRs::className(), ['zn_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getViajeDetalles()
    {
        return $this->hasMany(ViajeDetalle::className(), ['zona_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipoZona()
    {
        return $this->hasOne(TipoZonas::className(), ['id' => 'tipo_zona_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZonaCategoria()
    {
        return $this->hasOne(ZonasCategorias::className(), ['id' => 'zona_categoria_id']);
    }
}

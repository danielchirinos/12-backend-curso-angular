<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "viaje_detalle".
 *
 * @property int $id
 * @property int $viaje_id
 * @property int $zona_id
 * @property int|null $orden
 * @property string|null $fecha_entrada
 * @property string|null $fecha_salida
 * @property string|null $fecha_entrada_gps
 * @property string|null $fecha_salida_gps
 * @property int|null $alerta
 * @property int|null $kpi
 * @property string|null $notificacion_email
 * @property int $estado 0 / Sin Eventos a Tiempo 1 / Sin Eventos Atrasado 2 / Con Eventos a Tiempo 3 / Con Eventos atrasado
 * @property string|null $fecha_creado
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 * @property int|null $semaforo_id
 * @property string|null $eta
 * @property int|null $cambio_estado_manual
 * @property string|null $sobretiempo
 * @property string|null $tipo_sobretiempo
 * @property float|null $cobro_sobretiempo
 * @property string|null $temperatura1_entrada
 * @property string|null $temperatura1_salida
 * @property string|null $temperatura2_entrada
 * @property string|null $temperatura2_salida
 * @property int|null $cliente_direccion_id
 * @property int|null $sync
 * @property int|null $semaforo_id_out
 * @property int|null $sync_in
 * @property int|null $sync_out
 * @property int|null $horas_sobretiempo_transportista
 * @property float|null $valor_hora_sobretiempo_transportista
 * @property float|null $cobro_sobretiempo_transportista
 * @property int|null $macrozona_id
 * @property int|null $m_zona_id
 *
 * @property NotificacionConductor[] $notificacionConductors
 * @property ViajeDatosCarga[] $viajeDatosCargas
 * @property ClienteDirecciones $clienteDireccion
 * @property MZonas $mZona
 * @property Macrozonas $macrozona
 * @property Viajes $viaje
 * @property Zonas $zona
 * @property ViajeDetalleAccion[] $viajeDetalleAccions
 * @property ViajeDetallePod[] $viajeDetallePods
 * @property ViajeDetalleRampla[] $viajeDetalleRamplas
 * @property ViajeDocumento[] $viajeDocumentos
 * @property ViajeNovedades[] $viajeNovedades
 */
class ViajeDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'viaje_detalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['viaje_id', 'zona_id'], 'required'],
            [['viaje_id', 'zona_id', 'orden', 'alerta', 'kpi', 'estado', 'semaforo_id', 'cambio_estado_manual', 'cliente_direccion_id', 'sync', 'semaforo_id_out', 'sync_in', 'sync_out', 'horas_sobretiempo_transportista', 'macrozona_id', 'm_zona_id'], 'default', 'value' => null],
            [['viaje_id', 'zona_id', 'orden', 'alerta', 'kpi', 'estado', 'semaforo_id', 'cambio_estado_manual', 'cliente_direccion_id', 'sync', 'semaforo_id_out', 'sync_in', 'sync_out', 'horas_sobretiempo_transportista', 'macrozona_id', 'm_zona_id'], 'integer'],
            [['fecha_entrada', 'fecha_salida', 'fecha_entrada_gps', 'fecha_salida_gps', 'fecha_creado', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['sobretiempo', 'tipo_sobretiempo'], 'string'],
            [['cobro_sobretiempo', 'valor_hora_sobretiempo_transportista', 'cobro_sobretiempo_transportista'], 'number'],
            [['notificacion_email', 'eta', 'temperatura1_entrada', 'temperatura1_salida', 'temperatura2_entrada', 'temperatura2_salida'], 'string', 'max' => 255],
            [['cliente_direccion_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClienteDirecciones::className(), 'targetAttribute' => ['cliente_direccion_id' => 'id']],
            [['m_zona_id'], 'exist', 'skipOnError' => true, 'targetClass' => MZonas::className(), 'targetAttribute' => ['m_zona_id' => 'id']],
            [['macrozona_id'], 'exist', 'skipOnError' => true, 'targetClass' => Macrozonas::className(), 'targetAttribute' => ['macrozona_id' => 'id']],
            [['viaje_id'], 'exist', 'skipOnError' => true, 'targetClass' => Viajes::className(), 'targetAttribute' => ['viaje_id' => 'id']],
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
            'viaje_id' => 'Viaje ID',
            'zona_id' => 'Zona ID',
            'orden' => 'Orden',
            'fecha_entrada' => 'Fecha Entrada',
            'fecha_salida' => 'Fecha Salida',
            'fecha_entrada_gps' => 'Fecha Entrada Gps',
            'fecha_salida_gps' => 'Fecha Salida Gps',
            'alerta' => 'Alerta',
            'kpi' => 'Kpi',
            'notificacion_email' => 'Notificacion Email',
            'estado' => 'Estado',
            'fecha_creado' => 'Fecha Creado',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
            'semaforo_id' => 'Semaforo ID',
            'eta' => 'Eta',
            'cambio_estado_manual' => 'Cambio Estado Manual',
            'sobretiempo' => 'Sobretiempo',
            'tipo_sobretiempo' => 'Tipo Sobretiempo',
            'cobro_sobretiempo' => 'Cobro Sobretiempo',
            'temperatura1_entrada' => 'Temperatura1 Entrada',
            'temperatura1_salida' => 'Temperatura1 Salida',
            'temperatura2_entrada' => 'Temperatura2 Entrada',
            'temperatura2_salida' => 'Temperatura2 Salida',
            'cliente_direccion_id' => 'Cliente Direccion ID',
            'sync' => 'Sync',
            'semaforo_id_out' => 'Semaforo Id Out',
            'sync_in' => 'Sync In',
            'sync_out' => 'Sync Out',
            'horas_sobretiempo_transportista' => 'Horas Sobretiempo Transportista',
            'valor_hora_sobretiempo_transportista' => 'Valor Hora Sobretiempo Transportista',
            'cobro_sobretiempo_transportista' => 'Cobro Sobretiempo Transportista',
            'macrozona_id' => 'Macrozona ID',
            'm_zona_id' => 'M Zona ID',
        ];
    }

    /**
     * Gets query for [[NotificacionConductors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNotificacionConductors()
    {
        return $this->hasMany(NotificacionConductor::className(), ['viaje_detalle_id' => 'id']);
    }

    /**
     * Gets query for [[ViajeDatosCargas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajeDatosCargas()
    {
        return $this->hasMany(ViajeDatosCarga::className(), ['viaje_detalle_id' => 'id']);
    }

    /**
     * Gets query for [[ClienteDireccion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClienteDireccion()
    {
        return $this->hasOne(ClienteDirecciones::className(), ['id' => 'cliente_direccion_id']);
    }

    /**
     * Gets query for [[MZona]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMZona()
    {
        return $this->hasOne(MZonas::className(), ['id' => 'm_zona_id']);
    }

    /**
     * Gets query for [[Macrozona]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMacrozona()
    {
        return $this->hasOne(Macrozonas::className(), ['id' => 'macrozona_id']);
    }

    /**
     * Gets query for [[Viaje]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViaje()
    {
        return $this->hasOne(Viajes::className(), ['id' => 'viaje_id']);
    }

    /**
     * Gets query for [[Zona]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getZona()
    {
        return $this->hasOne(Zonas::className(), ['id' => 'zona_id']);
    }

    /**
     * Gets query for [[ViajeDetalleAccions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajeDetalleAccions()
    {
        return $this->hasMany(ViajeDetalleAccion::className(), ['viaje_detalle_id' => 'id']);
    }

    /**
     * Gets query for [[ViajeDetallePods]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajeDetallePods()
    {
        return $this->hasMany(ViajeDetallePod::className(), ['viaje_detalle_id' => 'id']);
    }

    /**
     * Gets query for [[ViajeDetalleRamplas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajeDetalleRamplas()
    {
        return $this->hasMany(ViajeDetalleRampla::className(), ['viaje_detalle_id' => 'id']);
    }

    /**
     * Gets query for [[ViajeDocumentos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajeDocumentos()
    {
        return $this->hasMany(ViajeDocumento::className(), ['viaje_detalle_id' => 'id']);
    }

    /**
     * Gets query for [[ViajeNovedades]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajeNovedades()
    {
        return $this->hasMany(ViajeNovedades::className(), ['viaje_detalle_id' => 'id']);
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "viajes".
 *
 * @property int $id
 * @property string|null $nro_viaje Nro de viaje colocado por cliente
 * @property int $tipo_servicio_id Tipo de Viaje
 * @property int $cliente_id Cliente
 * @property int|null $transportista_id Transportista
 * @property int|null $vehiculo_uno_id Patente Principal
 * @property int|null $vehiculo_dos_id Patente Secundaria
 * @property int|null $conductor_id Conductor
 * @property int $estatus_viaje_id Estatus
 * @property string|null $fecha Fecha del Viaje
 * @property bool $procesado
 * @property string|null $observacion
 * @property int|null $poligono_cliente
 * @property int|null $subestatus_viaje_id
 * @property int $activar_ruta_segura
 * @property int|null $id_ruta_segura
 * @property int|null $sync
 * @property int $tipo_viaje_id
 * @property int|null $ruta_id
 * @property int|null $prefactura_id
 * @property int|null $proforma_id
 * @property int|null $temperatura_perfil_id
 * @property int|null $tipo_contenedor_id
 * @property int|null $uso_chasis_id
 * @property int|null $unidad_negocio_id
 * @property string|null $fecha_presentacion
 * @property int|null $conductor_dos_id
 * @property int|null $hoja_ruta_id
 * @property int|null $tipo_comision_id 1 para 50 y 50, 2 para 100 y 100, 3 solo conductor principal
 * @property int|null $liquidacion_uno_id liquidacion del coductor 1
 * @property int|null $liquidacion_dos_id liquidacion del conducto 2
 * @property int|null $contrato_costo_id
 * @property int|null $contrato_venta_id
 * @property float|null $tarifa_costo
 * @property float|null $tarifa_venta
 * @property float|null $monto_aprovisionado
 * @property int|null $carga_id
 * @property int|null $cliente_contacto_id
 * @property string|null $numero_planilla
 * @property int|null $kg
 * @property int|null $liquidacion_id
 *
 * @property CajaVale[] $cajaVales
 * @property EventosTemperatura[] $eventosTemperaturas
 * @property EventosTemperaturaCiclos[] $eventosTemperaturaCiclos
 * @property LiquidacionDetalle[] $liquidacionDetalles
 * @property NotificacionConductor[] $notificacionConductors
 * @property PrefacturaDetalle[] $prefacturaDetalles
 * @property PrefacturaProformaCambioTarifa[] $prefacturaProformaCambioTarifas
 * @property ProformaDetalle[] $proformaDetalles
 * @property Rendicion[] $rendicions
 * @property RendicionAutorizacion[] $rendicionAutorizacions
 * @property ViajeAccion[] $viajeAccions
 * @property ViajeCierreAdministrativo[] $viajeCierreAdministrativos
 * @property ViajeDatosCarga[] $viajeDatosCargas
 * @property ViajeDetalle[] $viajeDetalles
 * @property ViajeDocumento[] $viajeDocumentos
 * @property ViajeRutaPropiedades[] $viajeRutaPropiedades
 * @property Carga $carga
 * @property Clientes $cliente
 * @property ClientesContactos $clienteContacto
 * @property Conductores $conductor
 * @property Conductores $conductorDos
 * @property Contrato $contratoCosto
 * @property Contrato $contratoVenta
 * @property EstatusViaje $estatusViaje
 * @property HojaRuta $hojaRuta
 * @property Liquidacion $liquidacion
 * @property Prefactura $prefactura
 * @property Proforma $proforma
 * @property Ruta $ruta
 * @property SubestatusViaje $subestatusViaje
 * @property TemperaturaPerfiles $temperaturaPerfil
 * @property TipoContenedor $tipoContenedor
 * @property TipoServicio $tipoServicio
 * @property TipoViajes $tipoViaje
 * @property Transportistas $transportista
 * @property UnidadNegocio $unidadNegocio
 * @property UsoChasis $usoChasis
 * @property Vehiculos $vehiculoUno
 * @property Vehiculos $vehiculoDos
 * @property ViajesCamposOpcionales[] $viajesCamposOpcionales
 * @property ViajesCierreOperativo $viajesCierreOperativo
 * @property ViajesLog[] $viajesLogs
 * @property ViajesServiciosAdicionales[] $viajesServiciosAdicionales
 */
class Viajes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'viajes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nro_viaje'], 'string'],
            [['tipo_servicio_id', 'cliente_id'], 'required'],
            [['tipo_servicio_id', 'cliente_id', 'transportista_id', 'vehiculo_uno_id', 'vehiculo_dos_id', 'conductor_id', 'estatus_viaje_id', 'poligono_cliente', 'subestatus_viaje_id', 'activar_ruta_segura', 'id_ruta_segura', 'sync', 'tipo_viaje_id', 'ruta_id', 'prefactura_id', 'proforma_id', 'temperatura_perfil_id', 'tipo_contenedor_id', 'uso_chasis_id', 'unidad_negocio_id', 'conductor_dos_id', 'hoja_ruta_id', 'tipo_comision_id', 'liquidacion_uno_id', 'liquidacion_dos_id', 'contrato_costo_id', 'contrato_venta_id', 'carga_id', 'cliente_contacto_id', 'kg', 'liquidacion_id'], 'default', 'value' => null],
            [['tipo_servicio_id', 'cliente_id', 'transportista_id', 'vehiculo_uno_id', 'vehiculo_dos_id', 'conductor_id', 'estatus_viaje_id', 'poligono_cliente', 'subestatus_viaje_id', 'activar_ruta_segura', 'id_ruta_segura', 'sync', 'tipo_viaje_id', 'ruta_id', 'prefactura_id', 'proforma_id', 'temperatura_perfil_id', 'tipo_contenedor_id', 'uso_chasis_id', 'unidad_negocio_id', 'conductor_dos_id', 'hoja_ruta_id', 'tipo_comision_id', 'liquidacion_uno_id', 'liquidacion_dos_id', 'contrato_costo_id', 'contrato_venta_id', 'carga_id', 'cliente_contacto_id', 'kg', 'liquidacion_id'], 'integer'],
            [['fecha', 'fecha_presentacion'], 'safe'],
            [['procesado'], 'boolean'],
            [['tarifa_costo', 'tarifa_venta', 'monto_aprovisionado'], 'number'],
            [['observacion', 'numero_planilla'], 'string', 'max' => 255],
            [['nro_viaje'], 'unique'],
            [['carga_id'], 'exist', 'skipOnError' => true, 'targetClass' => Carga::className(), 'targetAttribute' => ['carga_id' => 'id']],
            [['cliente_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clientes::className(), 'targetAttribute' => ['cliente_id' => 'id']],
            [['cliente_contacto_id'], 'exist', 'skipOnError' => true, 'targetClass' => ClientesContactos::className(), 'targetAttribute' => ['cliente_contacto_id' => 'id']],
            [['conductor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Conductores::className(), 'targetAttribute' => ['conductor_id' => 'id']],
            [['conductor_dos_id'], 'exist', 'skipOnError' => true, 'targetClass' => Conductores::className(), 'targetAttribute' => ['conductor_dos_id' => 'id']],
            [['contrato_costo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contrato::className(), 'targetAttribute' => ['contrato_costo_id' => 'id']],
            [['contrato_venta_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contrato::className(), 'targetAttribute' => ['contrato_venta_id' => 'id']],
            [['estatus_viaje_id'], 'exist', 'skipOnError' => true, 'targetClass' => EstatusViaje::className(), 'targetAttribute' => ['estatus_viaje_id' => 'id']],
            [['hoja_ruta_id'], 'exist', 'skipOnError' => true, 'targetClass' => HojaRuta::className(), 'targetAttribute' => ['hoja_ruta_id' => 'id']],
            [['liquidacion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Liquidacion::className(), 'targetAttribute' => ['liquidacion_id' => 'id']],
            [['prefactura_id'], 'exist', 'skipOnError' => true, 'targetClass' => Prefactura::className(), 'targetAttribute' => ['prefactura_id' => 'id']],
            [['proforma_id'], 'exist', 'skipOnError' => true, 'targetClass' => Proforma::className(), 'targetAttribute' => ['proforma_id' => 'id']],
            [['ruta_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ruta::className(), 'targetAttribute' => ['ruta_id' => 'id']],
            [['subestatus_viaje_id'], 'exist', 'skipOnError' => true, 'targetClass' => SubestatusViaje::className(), 'targetAttribute' => ['subestatus_viaje_id' => 'id']],
            [['temperatura_perfil_id'], 'exist', 'skipOnError' => true, 'targetClass' => TemperaturaPerfiles::className(), 'targetAttribute' => ['temperatura_perfil_id' => 'id']],
            [['tipo_contenedor_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoContenedor::className(), 'targetAttribute' => ['tipo_contenedor_id' => 'id']],
            [['tipo_servicio_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoServicio::className(), 'targetAttribute' => ['tipo_servicio_id' => 'id']],
            [['tipo_viaje_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoViajes::className(), 'targetAttribute' => ['tipo_viaje_id' => 'id']],
            [['transportista_id'], 'exist', 'skipOnError' => true, 'targetClass' => Transportistas::className(), 'targetAttribute' => ['transportista_id' => 'id']],
            [['unidad_negocio_id'], 'exist', 'skipOnError' => true, 'targetClass' => UnidadNegocio::className(), 'targetAttribute' => ['unidad_negocio_id' => 'id']],
            [['uso_chasis_id'], 'exist', 'skipOnError' => true, 'targetClass' => UsoChasis::className(), 'targetAttribute' => ['uso_chasis_id' => 'id']],
            [['vehiculo_uno_id'], 'exist', 'skipOnError' => true, 'targetClass' => Vehiculos::className(), 'targetAttribute' => ['vehiculo_uno_id' => 'id']],
            [['vehiculo_dos_id'], 'exist', 'skipOnError' => true, 'targetClass' => Vehiculos::className(), 'targetAttribute' => ['vehiculo_dos_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nro_viaje' => 'Nro Viaje',
            'tipo_servicio_id' => 'Tipo Servicio ID',
            'cliente_id' => 'Cliente ID',
            'transportista_id' => 'Transportista ID',
            'vehiculo_uno_id' => 'Vehiculo Uno ID',
            'vehiculo_dos_id' => 'Vehiculo Dos ID',
            'conductor_id' => 'Conductor ID',
            'estatus_viaje_id' => 'Estatus Viaje ID',
            'fecha' => 'Fecha',
            'procesado' => 'Procesado',
            'observacion' => 'Observacion',
            'poligono_cliente' => 'Poligono Cliente',
            'subestatus_viaje_id' => 'Subestatus Viaje ID',
            'activar_ruta_segura' => 'Activar Ruta Segura',
            'id_ruta_segura' => 'Id Ruta Segura',
            'sync' => 'Sync',
            'tipo_viaje_id' => 'Tipo Viaje ID',
            'ruta_id' => 'Ruta ID',
            'prefactura_id' => 'Prefactura ID',
            'proforma_id' => 'Proforma ID',
            'temperatura_perfil_id' => 'Temperatura Perfil ID',
            'tipo_contenedor_id' => 'Tipo Contenedor ID',
            'uso_chasis_id' => 'Uso Chasis ID',
            'unidad_negocio_id' => 'Unidad Negocio ID',
            'fecha_presentacion' => 'Fecha Presentacion',
            'conductor_dos_id' => 'Conductor Dos ID',
            'hoja_ruta_id' => 'Hoja Ruta ID',
            'tipo_comision_id' => 'Tipo Comision ID',
            'liquidacion_uno_id' => 'Liquidacion Uno ID',
            'liquidacion_dos_id' => 'Liquidacion Dos ID',
            'contrato_costo_id' => 'Contrato Costo ID',
            'contrato_venta_id' => 'Contrato Venta ID',
            'tarifa_costo' => 'Tarifa Costo',
            'tarifa_venta' => 'Tarifa Venta',
            'monto_aprovisionado' => 'Monto Aprovisionado',
            'carga_id' => 'Carga ID',
            'cliente_contacto_id' => 'Cliente Contacto ID',
            'numero_planilla' => 'Numero Planilla',
            'kg' => 'Kg',
            'liquidacion_id' => 'Liquidacion ID',
        ];
    }

    /**
     * Gets query for [[CajaVales]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCajaVales()
    {
        return $this->hasMany(CajaVale::className(), ['viaje_id' => 'id']);
    }

    /**
     * Gets query for [[EventosTemperaturas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEventosTemperaturas()
    {
        return $this->hasMany(EventosTemperatura::className(), ['viaje_id' => 'id']);
    }

    /**
     * Gets query for [[EventosTemperaturaCiclos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEventosTemperaturaCiclos()
    {
        return $this->hasMany(EventosTemperaturaCiclos::className(), ['viaje_id' => 'id']);
    }

    /**
     * Gets query for [[LiquidacionDetalles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLiquidacionDetalles()
    {
        return $this->hasMany(LiquidacionDetalle::className(), ['viaje_id' => 'id']);
    }

    /**
     * Gets query for [[NotificacionConductors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNotificacionConductors()
    {
        return $this->hasMany(NotificacionConductor::className(), ['viaje_id' => 'id']);
    }

    /**
     * Gets query for [[PrefacturaDetalles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrefacturaDetalles()
    {
        return $this->hasMany(PrefacturaDetalle::className(), ['viaje_id' => 'id']);
    }

    /**
     * Gets query for [[PrefacturaProformaCambioTarifas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrefacturaProformaCambioTarifas()
    {
        return $this->hasMany(PrefacturaProformaCambioTarifa::className(), ['viaje_id' => 'id']);
    }

    /**
     * Gets query for [[ProformaDetalles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProformaDetalles()
    {
        return $this->hasMany(ProformaDetalle::className(), ['viaje_id' => 'id']);
    }

    /**
     * Gets query for [[Rendicions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRendicions()
    {
        return $this->hasMany(Rendicion::className(), ['viaje_id' => 'id']);
    }

    /**
     * Gets query for [[RendicionAutorizacions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRendicionAutorizacions()
    {
        return $this->hasMany(RendicionAutorizacion::className(), ['viaje_id' => 'id']);
    }

    /**
     * Gets query for [[ViajeAccions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajeAccions()
    {
        return $this->hasMany(ViajeAccion::className(), ['viaje_id' => 'id']);
    }

    /**
     * Gets query for [[ViajeCierreAdministrativos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajeCierreAdministrativos()
    {
        return $this->hasMany(ViajeCierreAdministrativo::className(), ['viaje_id' => 'id']);
    }

    /**
     * Gets query for [[ViajeDatosCargas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajeDatosCargas()
    {
        return $this->hasMany(ViajeDatosCarga::className(), ['viaje_id' => 'id']);
    }

    /**
     * Gets query for [[ViajeDetalles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajeDetalles()
    {
        return $this->hasMany(ViajeDetalle::className(), ['viaje_id' => 'id']);
    }

    /**
     * Gets query for [[ViajeDocumentos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajeDocumentos()
    {
        return $this->hasMany(ViajeDocumento::className(), ['viaje_id' => 'id']);
    }

    /**
     * Gets query for [[ViajeRutaPropiedades]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajeRutaPropiedades()
    {
        return $this->hasMany(ViajeRutaPropiedades::className(), ['viaje_id' => 'id']);
    }

    /**
     * Gets query for [[Carga]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCarga()
    {
        return $this->hasOne(Carga::className(), ['id' => 'carga_id']);
    }

    /**
     * Gets query for [[Cliente]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCliente()
    {
        return $this->hasOne(Clientes::className(), ['id' => 'cliente_id']);
    }

    /**
     * Gets query for [[ClienteContacto]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClienteContacto()
    {
        return $this->hasOne(ClientesContactos::className(), ['id' => 'cliente_contacto_id']);
    }

    /**
     * Gets query for [[Conductor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getConductor()
    {
        return $this->hasOne(Conductores::className(), ['id' => 'conductor_id']);
    }

    /**
     * Gets query for [[ConductorDos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getConductorDos()
    {
        return $this->hasOne(Conductores::className(), ['id' => 'conductor_dos_id']);
    }

    /**
     * Gets query for [[ContratoCosto]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContratoCosto()
    {
        return $this->hasOne(Contrato::className(), ['id' => 'contrato_costo_id']);
    }

    /**
     * Gets query for [[ContratoVenta]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContratoVenta()
    {
        return $this->hasOne(Contrato::className(), ['id' => 'contrato_venta_id']);
    }

    /**
     * Gets query for [[EstatusViaje]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEstatusViaje()
    {
        return $this->hasOne(EstatusViaje::className(), ['id' => 'estatus_viaje_id']);
    }

    /**
     * Gets query for [[HojaRuta]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHojaRuta()
    {
        return $this->hasOne(HojaRuta::className(), ['id' => 'hoja_ruta_id']);
    }

    /**
     * Gets query for [[Liquidacion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLiquidacion()
    {
        return $this->hasOne(Liquidacion::className(), ['id' => 'liquidacion_id']);
    }

    /**
     * Gets query for [[Prefactura]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrefactura()
    {
        return $this->hasOne(Prefactura::className(), ['id' => 'prefactura_id']);
    }

    /**
     * Gets query for [[Proforma]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProforma()
    {
        return $this->hasOne(Proforma::className(), ['id' => 'proforma_id']);
    }

    /**
     * Gets query for [[Ruta]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRuta()
    {
        return $this->hasOne(Ruta::className(), ['id' => 'ruta_id']);
    }

    /**
     * Gets query for [[SubestatusViaje]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubestatusViaje()
    {
        return $this->hasOne(SubestatusViaje::className(), ['id' => 'subestatus_viaje_id']);
    }

    /**
     * Gets query for [[TemperaturaPerfil]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTemperaturaPerfil()
    {
        return $this->hasOne(TemperaturaPerfiles::className(), ['id' => 'temperatura_perfil_id']);
    }

    /**
     * Gets query for [[TipoContenedor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTipoContenedor()
    {
        return $this->hasOne(TipoContenedor::className(), ['id' => 'tipo_contenedor_id']);
    }

    /**
     * Gets query for [[TipoServicio]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTipoServicio()
    {
        return $this->hasOne(TipoServicio::className(), ['id' => 'tipo_servicio_id']);
    }

    /**
     * Gets query for [[TipoViaje]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTipoViaje()
    {
        return $this->hasOne(TipoViajes::className(), ['id' => 'tipo_viaje_id']);
    }

    /**
     * Gets query for [[Transportista]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTransportista()
    {
        return $this->hasOne(Transportistas::className(), ['id' => 'transportista_id']);
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
     * Gets query for [[UsoChasis]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsoChasis()
    {
        return $this->hasOne(UsoChasis::className(), ['id' => 'uso_chasis_id']);
    }

    /**
     * Gets query for [[VehiculoUno]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVehiculoUno()
    {
        return $this->hasOne(Vehiculos::className(), ['id' => 'vehiculo_uno_id']);
    }

    /**
     * Gets query for [[VehiculoDos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVehiculoDos()
    {
        return $this->hasOne(Vehiculos::className(), ['id' => 'vehiculo_dos_id']);
    }

    /**
     * Gets query for [[ViajesCamposOpcionales]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajesCamposOpcionales()
    {
        return $this->hasMany(ViajesCamposOpcionales::className(), ['viaje_id' => 'id']);
    }

    /**
     * Gets query for [[ViajesCierreOperativo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajesCierreOperativo()
    {
        return $this->hasOne(ViajesCierreOperativo::className(), ['viaje_id' => 'id']);
    }

    /**
     * Gets query for [[ViajesLogs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajesLogs()
    {
        return $this->hasMany(ViajesLog::className(), ['viaje_id' => 'id']);
    }

    /**
     * Gets query for [[ViajesServiciosAdicionales]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajesServiciosAdicionales()
    {
        return $this->hasMany(ViajesServiciosAdicionales::className(), ['viaje_id' => 'id']);
    }
}

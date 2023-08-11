<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contrato".
 *
 * @property int $id
 * @property int $nro_contrato
 * @property int $estado 1 Activo / 2 Finalizado
 * @property int|null $cliente_id
 * @property int|null $tipo_operacion_id
 * @property int $ciclo_pago_id
 * @property int|null $transportista_id
 * @property int|null $tipo_tarifa_contrato_id 1 Venta / 2 Costo
 * @property int $unidad_negocio_id
 * @property string|null $fecha_vencimiento
 * @property float|null $valor_hora
 * @property string|null $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 * @property int $condicion_venta_dias cantidad de dias para pagarla
 *
 * @property CicloPagos $cicloPago
 * @property Clientes $cliente
 * @property TipoOperacion $tipoOperacion
 * @property TipoTarifa $tipoTarifaContrato
 * @property TipoTarifa $tipoTarifaContrato0
 * @property Transportistas $transportista
 * @property UnidadNegocio $unidadNegocio
 * @property ContratoAdjuntos[] $contratoAdjuntos
 * @property ContratoDocumento[] $contratoDocumentos
 * @property ContratoLog[] $contratoLogs
 * @property ContratoRuta[] $contratoRutas
 * @property ContratoServicioAdicional[] $contratoServicioAdicionals
 * @property Viajes[] $viajes
 * @property Viajes[] $viajes0
 */
class Contrato extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contrato';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nro_contrato', 'estado', 'ciclo_pago_id', 'unidad_negocio_id', 'condicion_venta_dias'], 'required'],
            [['nro_contrato', 'estado', 'cliente_id', 'tipo_operacion_id', 'ciclo_pago_id', 'transportista_id', 'tipo_tarifa_contrato_id', 'unidad_negocio_id', 'condicion_venta_dias'], 'default', 'value' => null],
            [['nro_contrato', 'estado', 'cliente_id', 'tipo_operacion_id', 'ciclo_pago_id', 'transportista_id', 'tipo_tarifa_contrato_id', 'unidad_negocio_id', 'condicion_venta_dias'], 'integer'],
            [['fecha_vencimiento', 'fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['valor_hora'], 'number'],
            [['nro_contrato'], 'unique'],
            [['ciclo_pago_id'], 'exist', 'skipOnError' => true, 'targetClass' => CicloPagos::className(), 'targetAttribute' => ['ciclo_pago_id' => 'id']],
            [['cliente_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clientes::className(), 'targetAttribute' => ['cliente_id' => 'id']],
            [['tipo_operacion_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoOperacion::className(), 'targetAttribute' => ['tipo_operacion_id' => 'id']],
            [['tipo_tarifa_contrato_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoTarifa::className(), 'targetAttribute' => ['tipo_tarifa_contrato_id' => 'id']],
            [['tipo_tarifa_contrato_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoTarifa::className(), 'targetAttribute' => ['tipo_tarifa_contrato_id' => 'id']],
            [['transportista_id'], 'exist', 'skipOnError' => true, 'targetClass' => Transportistas::className(), 'targetAttribute' => ['transportista_id' => 'id']],
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
            'nro_contrato' => 'Nro Contrato',
            'estado' => 'Estado',
            'cliente_id' => 'Cliente ID',
            'tipo_operacion_id' => 'Tipo Operacion ID',
            'ciclo_pago_id' => 'Ciclo Pago ID',
            'transportista_id' => 'Transportista ID',
            'tipo_tarifa_contrato_id' => 'Tipo Tarifa Contrato ID',
            'unidad_negocio_id' => 'Unidad Negocio ID',
            'fecha_vencimiento' => 'Fecha Vencimiento',
            'valor_hora' => 'Valor Hora',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
            'condicion_venta_dias' => 'Condicion Venta Dias',
        ];
    }

    /**
     * Gets query for [[CicloPago]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCicloPago()
    {
        return $this->hasOne(CicloPagos::className(), ['id' => 'ciclo_pago_id']);
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
     * Gets query for [[TipoOperacion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTipoOperacion()
    {
        return $this->hasOne(TipoOperacion::className(), ['id' => 'tipo_operacion_id']);
    }

    /**
     * Gets query for [[TipoTarifaContrato]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTipoTarifaContrato()
    {
        return $this->hasOne(TipoTarifa::className(), ['id' => 'tipo_tarifa_contrato_id']);
    }

    /**
     * Gets query for [[TipoTarifaContrato0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTipoTarifaContrato0()
    {
        return $this->hasOne(TipoTarifa::className(), ['id' => 'tipo_tarifa_contrato_id']);
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
     * Gets query for [[ContratoAdjuntos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContratoAdjuntos()
    {
        return $this->hasMany(ContratoAdjuntos::className(), ['contrato_id' => 'id']);
    }

    /**
     * Gets query for [[ContratoDocumentos]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContratoDocumentos()
    {
        return $this->hasMany(ContratoDocumento::className(), ['contrato_id' => 'id']);
    }

    /**
     * Gets query for [[ContratoLogs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContratoLogs()
    {
        return $this->hasMany(ContratoLog::className(), ['contrato_id' => 'id']);
    }

    /**
     * Gets query for [[ContratoRutas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContratoRutas()
    {
        return $this->hasMany(ContratoRuta::className(), ['contrato_id' => 'id']);
    }

    /**
     * Gets query for [[ContratoServicioAdicionals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContratoServicioAdicionals()
    {
        return $this->hasMany(ContratoServicioAdicional::className(), ['contrato_id' => 'id']);
    }

    /**
     * Gets query for [[Viajes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajes()
    {
        return $this->hasMany(Viajes::className(), ['contrato_costo_id' => 'id']);
    }

    /**
     * Gets query for [[Viajes0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajes0()
    {
        return $this->hasMany(Viajes::className(), ['contrato_venta_id' => 'id']);
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "prefactura".
 *
 * @property int $id
 * @property int $estado_prefactura_id
 * @property int|null $prefactura_documento_referencia_id
 * @property int|null $cliente_id
 * @property float $valor_neto valor neto (suma de tarifaas, adicionales y sobretiempo)
 * @property float|null $descuento descuento agregado al valor neto
 * @property int|null $tipo_descuento 1 para procentaje, 2 para monto
 * @property float|null $subtotal monto con descuento aplicado
 * @property float|null $iva iva aplicado al valor del subtotal
 * @property float|null $total total final de la prefactura
 * @property string $fecha_emision fecha de emision de la prefactura
 * @property string|null $nro_factura
 * @property string|null $nro_oc
 * @property int|null $condicion_venta_id cantidad de dias para pagarla
 * @property string|null $fecha_vencimiento fecha de emision + comision_venta_dias
 * @property string|null $fecha_factura
 * @property string|null $fecha_orden_compra
 * @property string|null $numero_planilla
 * @property string|null $fecha_edicion
 * @property int|null $facturacion_glosa 0 para no facturar glosa 1 para si facturar glosa
 * @property int|null $envio_erp 0 no se envia, 1 para enviar erp
 * @property int|null $envio_nc 0 no se envia, 1 para enviar erp
 * @property string|null $nro_nc
 * @property string|null $fecha_nc
 * @property string|null $observacion_nc
 * @property string|null $descripcion_glosa
 * @property string|null $he
 * @property string|null $nro_atencion
 * @property string|null $nro_confirmacion
 *
 * @property Clientes $cliente
 * @property CondicionVenta $condicionVenta
 * @property EstadosPrefacturaProforma $estadoPrefactura
 * @property PrefacturaDetalle[] $prefacturaDetalles
 * @property PrefacturaDocumentosReferencia[] $prefacturaDocumentosReferencias
 * @property PrefacturaLog[] $prefacturaLogs
 * @property Viajes[] $viajes
 */
class Prefactura extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'prefactura';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['estado_prefactura_id', 'valor_neto', 'fecha_emision'], 'required'],
            [['estado_prefactura_id', 'prefactura_documento_referencia_id', 'cliente_id', 'tipo_descuento', 'condicion_venta_id', 'facturacion_glosa', 'envio_erp', 'envio_nc'], 'default', 'value' => null],
            [['estado_prefactura_id', 'prefactura_documento_referencia_id', 'cliente_id', 'tipo_descuento', 'condicion_venta_id', 'facturacion_glosa', 'envio_erp', 'envio_nc'], 'integer'],
            [['valor_neto', 'descuento', 'subtotal', 'iva', 'total'], 'number'],
            [['fecha_emision', 'fecha_vencimiento', 'fecha_factura', 'fecha_orden_compra', 'fecha_edicion', 'fecha_nc'], 'safe'],
            [['nro_nc', 'observacion_nc', 'descripcion_glosa'], 'string'],
            [['nro_factura', 'nro_oc', 'numero_planilla', 'he', 'nro_atencion', 'nro_confirmacion'], 'string', 'max' => 255],
            [['cliente_id'], 'exist', 'skipOnError' => true, 'targetClass' => Clientes::className(), 'targetAttribute' => ['cliente_id' => 'id']],
            [['condicion_venta_id'], 'exist', 'skipOnError' => true, 'targetClass' => CondicionVenta::className(), 'targetAttribute' => ['condicion_venta_id' => 'id']],
            [['estado_prefactura_id'], 'exist', 'skipOnError' => true, 'targetClass' => EstadosPrefacturaProforma::className(), 'targetAttribute' => ['estado_prefactura_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'estado_prefactura_id' => 'Estado Prefactura ID',
            'prefactura_documento_referencia_id' => 'Prefactura Documento Referencia ID',
            'cliente_id' => 'Cliente ID',
            'valor_neto' => 'Valor Neto',
            'descuento' => 'Descuento',
            'tipo_descuento' => 'Tipo Descuento',
            'subtotal' => 'Subtotal',
            'iva' => 'Iva',
            'total' => 'Total',
            'fecha_emision' => 'Fecha Emision',
            'nro_factura' => 'Nro Factura',
            'nro_oc' => 'Nro Oc',
            'condicion_venta_id' => 'Condicion Venta ID',
            'fecha_vencimiento' => 'Fecha Vencimiento',
            'fecha_factura' => 'Fecha Factura',
            'fecha_orden_compra' => 'Fecha Orden Compra',
            'numero_planilla' => 'Numero Planilla',
            'fecha_edicion' => 'Fecha Edicion',
            'facturacion_glosa' => 'Facturacion Glosa',
            'envio_erp' => 'Envio Erp',
            'envio_nc' => 'Envio Nc',
            'nro_nc' => 'Nro Nc',
            'fecha_nc' => 'Fecha Nc',
            'observacion_nc' => 'Observacion Nc',
            'descripcion_glosa' => 'Descripcion Glosa',
            'he' => 'He',
            'nro_atencion' => 'Nro Atencion',
            'nro_confirmacion' => 'Nro Confirmacion',
        ];
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
     * Gets query for [[CondicionVenta]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCondicionVenta()
    {
        return $this->hasOne(CondicionVenta::className(), ['id' => 'condicion_venta_id']);
    }

    /**
     * Gets query for [[EstadoPrefactura]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEstadoPrefactura()
    {
        return $this->hasOne(EstadosPrefacturaProforma::className(), ['id' => 'estado_prefactura_id']);
    }

    /**
     * Gets query for [[PrefacturaDetalles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrefacturaDetalles()
    {
        return $this->hasMany(PrefacturaDetalle::className(), ['prefactura_id' => 'id']);
    }

    /**
     * Gets query for [[PrefacturaDocumentosReferencias]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrefacturaDocumentosReferencias()
    {
        return $this->hasMany(PrefacturaDocumentosReferencia::className(), ['prefactura_id' => 'id']);
    }

    /**
     * Gets query for [[PrefacturaLogs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrefacturaLogs()
    {
        return $this->hasMany(PrefacturaLog::className(), ['prefactura_id' => 'id']);
    }

    /**
     * Gets query for [[Viajes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajes()
    {
        return $this->hasMany(Viajes::className(), ['prefactura_id' => 'id']);
    }
}

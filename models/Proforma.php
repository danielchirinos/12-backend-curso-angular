<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "proforma".
 *
 * @property int $id
 * @property int $estado_proforma_id
 * @property string|null $nro_oc
 * @property int|null $transportista_id
 * @property int $transportista_contacto_id
 * @property int $unidad_negocio_id
 * @property float $valor_neto valor neto (suma de tarifaas, adicionales y sobretiempo)
 * @property float|null $descuento descuento agregado al valor neto
 * @property int|null $tipo_descuento 1 para procentaje, 2 para monto
 * @property float|null $subtotal monto con descuento aplicado
 * @property float|null $iva iva aplicado al valor del subtotal
 * @property float|null $total total final de la proforma
 * @property string $fecha_emision fecha de emision de la proforma
 *
 * @property EstadosPrefacturaProforma $estadoProforma
 * @property Transportistas $transportista
 * @property TransportistasContactos $transportistaContacto
 * @property UnidadNegocio $unidadNegocio
 * @property ProformaDetalle[] $proformaDetalles
 * @property Viajes[] $viajes
 */
class Proforma extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'proforma';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['estado_proforma_id', 'transportista_contacto_id', 'unidad_negocio_id', 'valor_neto', 'fecha_emision'], 'required'],
            [['estado_proforma_id', 'transportista_id', 'transportista_contacto_id', 'unidad_negocio_id', 'tipo_descuento'], 'default', 'value' => null],
            [['estado_proforma_id', 'transportista_id', 'transportista_contacto_id', 'unidad_negocio_id', 'tipo_descuento'], 'integer'],
            [['valor_neto', 'descuento', 'subtotal', 'iva', 'total'], 'number'],
            [['fecha_emision'], 'safe'],
            [['nro_oc'], 'string', 'max' => 255],
            [['estado_proforma_id'], 'exist', 'skipOnError' => true, 'targetClass' => EstadosPrefacturaProforma::className(), 'targetAttribute' => ['estado_proforma_id' => 'id']],
            [['transportista_id'], 'exist', 'skipOnError' => true, 'targetClass' => Transportistas::className(), 'targetAttribute' => ['transportista_id' => 'id']],
            [['transportista_contacto_id'], 'exist', 'skipOnError' => true, 'targetClass' => TransportistasContactos::className(), 'targetAttribute' => ['transportista_contacto_id' => 'id']],
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
            'estado_proforma_id' => 'Estado Proforma ID',
            'nro_oc' => 'Nro Oc',
            'transportista_id' => 'Transportista ID',
            'transportista_contacto_id' => 'Transportista Contacto ID',
            'unidad_negocio_id' => 'Unidad Negocio ID',
            'valor_neto' => 'Valor Neto',
            'descuento' => 'Descuento',
            'tipo_descuento' => 'Tipo Descuento',
            'subtotal' => 'Subtotal',
            'iva' => 'Iva',
            'total' => 'Total',
            'fecha_emision' => 'Fecha Emision',
        ];
    }

    /**
     * Gets query for [[EstadoProforma]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEstadoProforma()
    {
        return $this->hasOne(EstadosPrefacturaProforma::className(), ['id' => 'estado_proforma_id']);
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
     * Gets query for [[TransportistaContacto]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTransportistaContacto()
    {
        return $this->hasOne(TransportistasContactos::className(), ['id' => 'transportista_contacto_id']);
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
     * Gets query for [[ProformaDetalles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProformaDetalles()
    {
        return $this->hasMany(ProformaDetalle::className(), ['proforma_id' => 'id']);
    }

    /**
     * Gets query for [[Viajes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getViajes()
    {
        return $this->hasMany(Viajes::className(), ['proforma_id' => 'id']);
    }
}

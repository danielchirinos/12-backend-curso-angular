<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "caja_vale".
 *
 * @property int $id
 * @property int $viaje_id
 * @property int $caja_id
 * @property int $tipo_vale_id
 * @property int $usuario_solicitud_id
 * @property int|null $usuario_autoriza_id
 * @property float|null $monto
 * @property string|null $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 * @property int|null $transferencia_rendicion_id
 * @property string|null $fecha_entregado
 * @property int|null $tipo_pago 1 efectivo, 2 transferencia
 * @property int|null $envio_erp Envio al ERP
 * @property int|null $estado_erp 1 OK ERP / 0 por defecto
 * @property int|null $motivo_aprovisionamiento_id
 *
 * @property Caja $caja
 * @property MotivoAprovisionamiento $motivoAprovisionamiento
 * @property TipoVale $tipoVale
 * @property Usuarios $usuarioSolicitud
 * @property Usuarios $usuarioSolicitud0
 * @property Viajes $viaje
 */
class CajaVale extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'caja_vale';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['viaje_id', 'caja_id', 'tipo_vale_id', 'usuario_solicitud_id'], 'required'],
            [['viaje_id', 'caja_id', 'tipo_vale_id', 'usuario_solicitud_id', 'usuario_autoriza_id', 'transferencia_rendicion_id', 'tipo_pago', 'envio_erp', 'estado_erp', 'motivo_aprovisionamiento_id'], 'default', 'value' => null],
            [['viaje_id', 'caja_id', 'tipo_vale_id', 'usuario_solicitud_id', 'usuario_autoriza_id', 'transferencia_rendicion_id', 'tipo_pago', 'envio_erp', 'estado_erp', 'motivo_aprovisionamiento_id'], 'integer'],
            [['monto'], 'number'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado', 'fecha_entregado'], 'safe'],
            [['caja_id'], 'exist', 'skipOnError' => true, 'targetClass' => Caja::className(), 'targetAttribute' => ['caja_id' => 'id']],
            [['motivo_aprovisionamiento_id'], 'exist', 'skipOnError' => true, 'targetClass' => MotivoAprovisionamiento::className(), 'targetAttribute' => ['motivo_aprovisionamiento_id' => 'id']],
            [['tipo_vale_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoVale::className(), 'targetAttribute' => ['tipo_vale_id' => 'id']],
            [['usuario_solicitud_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['usuario_solicitud_id' => 'id']],
            [['usuario_solicitud_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['usuario_solicitud_id' => 'id']],
            [['viaje_id'], 'exist', 'skipOnError' => true, 'targetClass' => Viajes::className(), 'targetAttribute' => ['viaje_id' => 'id']],
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
            'caja_id' => 'Caja ID',
            'tipo_vale_id' => 'Tipo Vale ID',
            'usuario_solicitud_id' => 'Usuario Solicitud ID',
            'usuario_autoriza_id' => 'Usuario Autoriza ID',
            'monto' => 'Monto',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
            'transferencia_rendicion_id' => 'Transferencia Rendicion ID',
            'fecha_entregado' => 'Fecha Entregado',
            'tipo_pago' => 'Tipo Pago',
            'envio_erp' => 'Envio Erp',
            'estado_erp' => 'Estado Erp',
            'motivo_aprovisionamiento_id' => 'Motivo Aprovisionamiento ID',
        ];
    }

    /**
     * Gets query for [[Caja]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCaja()
    {
        return $this->hasOne(Caja::className(), ['id' => 'caja_id']);
    }

    /**
     * Gets query for [[MotivoAprovisionamiento]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMotivoAprovisionamiento()
    {
        return $this->hasOne(MotivoAprovisionamiento::className(), ['id' => 'motivo_aprovisionamiento_id']);
    }

    /**
     * Gets query for [[TipoVale]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTipoVale()
    {
        return $this->hasOne(TipoVale::className(), ['id' => 'tipo_vale_id']);
    }

    /**
     * Gets query for [[UsuarioSolicitud]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarioSolicitud()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'usuario_solicitud_id']);
    }

    /**
     * Gets query for [[UsuarioSolicitud0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarioSolicitud0()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'usuario_solicitud_id']);
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
}

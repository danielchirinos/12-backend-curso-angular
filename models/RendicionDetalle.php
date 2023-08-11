<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rendicion_detalle".
 *
 * @property int $id
 * @property int $rendicion_id
 * @property int $categoria_rendicion_id
 * @property int $motivo_rendicion_id
 * @property float $monto
 * @property int $tipo_documento_id
 * @property string|null $nro_documento
 * @property string|null $razon_social
 * @property int $tipo_ingreso_id
 * @property string $foto
 * @property int|null $estatus
 * @property string|null $fecha_boleta
 * @property string|null $observaciones
 * @property string|null $fecha_revision
 * @property int|null $usuario_revision_id
 * @property string|null $rut_empresa
 * @property string|null $observacion
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 * @property int $validado 0 pendiente, 1 validado, 2 rechazado
 *
 * @property Rendicion $rendicion
 * @property RendicionCategoria $categoriaRendicion
 * @property RendicionMotivo $motivoRendicion
 * @property TipoDocumentos $tipoDocumento
 * @property TipoIngreso $tipoIngreso
 * @property Usuarios $usuarioRevision
 */
class RendicionDetalle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rendicion_detalle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['rendicion_id', 'categoria_rendicion_id', 'motivo_rendicion_id', 'monto', 'tipo_documento_id', 'tipo_ingreso_id', 'foto', 'fecha_creacion'], 'required'],
            [['rendicion_id', 'categoria_rendicion_id', 'motivo_rendicion_id', 'tipo_documento_id', 'tipo_ingreso_id', 'estatus', 'usuario_revision_id', 'validado'], 'default', 'value' => null],
            [['rendicion_id', 'categoria_rendicion_id', 'motivo_rendicion_id', 'tipo_documento_id', 'tipo_ingreso_id', 'estatus', 'usuario_revision_id', 'validado'], 'integer'],
            [['monto'], 'number'],
            [['fecha_boleta', 'fecha_revision', 'fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['observaciones'], 'string'],
            [['nro_documento', 'razon_social', 'foto', 'rut_empresa', 'observacion'], 'string', 'max' => 255],
            [['rendicion_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rendicion::className(), 'targetAttribute' => ['rendicion_id' => 'id']],
            [['categoria_rendicion_id'], 'exist', 'skipOnError' => true, 'targetClass' => RendicionCategoria::className(), 'targetAttribute' => ['categoria_rendicion_id' => 'id']],
            [['motivo_rendicion_id'], 'exist', 'skipOnError' => true, 'targetClass' => RendicionMotivo::className(), 'targetAttribute' => ['motivo_rendicion_id' => 'id']],
            [['tipo_documento_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoDocumentos::className(), 'targetAttribute' => ['tipo_documento_id' => 'id']],
            [['tipo_ingreso_id'], 'exist', 'skipOnError' => true, 'targetClass' => TipoIngreso::className(), 'targetAttribute' => ['tipo_ingreso_id' => 'id']],
            [['usuario_revision_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['usuario_revision_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rendicion_id' => 'Rendicion ID',
            'categoria_rendicion_id' => 'Categoria Rendicion ID',
            'motivo_rendicion_id' => 'Motivo Rendicion ID',
            'monto' => 'Monto',
            'tipo_documento_id' => 'Tipo Documento ID',
            'nro_documento' => 'Nro Documento',
            'razon_social' => 'Razon Social',
            'tipo_ingreso_id' => 'Tipo Ingreso ID',
            'foto' => 'Foto',
            'estatus' => 'Estatus',
            'fecha_boleta' => 'Fecha Boleta',
            'observaciones' => 'Observaciones',
            'fecha_revision' => 'Fecha Revision',
            'usuario_revision_id' => 'Usuario Revision ID',
            'rut_empresa' => 'Rut Empresa',
            'observacion' => 'Observacion',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
            'validado' => 'Validado',
        ];
    }

    /**
     * Gets query for [[Rendicion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRendicion()
    {
        return $this->hasOne(Rendicion::className(), ['id' => 'rendicion_id']);
    }

    /**
     * Gets query for [[CategoriaRendicion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategoriaRendicion()
    {
        return $this->hasOne(RendicionCategoria::className(), ['id' => 'categoria_rendicion_id']);
    }

    /**
     * Gets query for [[MotivoRendicion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMotivoRendicion()
    {
        return $this->hasOne(RendicionMotivo::className(), ['id' => 'motivo_rendicion_id']);
    }

    /**
     * Gets query for [[TipoDocumento]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTipoDocumento()
    {
        return $this->hasOne(TipoDocumentos::className(), ['id' => 'tipo_documento_id']);
    }

    /**
     * Gets query for [[TipoIngreso]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTipoIngreso()
    {
        return $this->hasOne(TipoIngreso::className(), ['id' => 'tipo_ingreso_id']);
    }

    /**
     * Gets query for [[UsuarioRevision]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarioRevision()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'usuario_revision_id']);
    }
}

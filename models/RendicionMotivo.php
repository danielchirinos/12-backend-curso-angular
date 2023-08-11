<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rendicion_motivo".
 *
 * @property int $id
 * @property string $nombre
 * @property int $rendicion_categoria_id
 * @property string $fecha_creacion
 * @property string|null $fecha_edicion
 * @property string|null $fecha_borrado
 * @property string|null $cuenta_contable
 *
 * @property RendicionDetalle[] $rendicionDetalles
 * @property RendicionCategoria $rendicionCategoria
 */
class RendicionMotivo extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rendicion_motivo';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nombre', 'rendicion_categoria_id', 'fecha_creacion'], 'required'],
            [['rendicion_categoria_id'], 'default', 'value' => null],
            [['rendicion_categoria_id'], 'integer'],
            [['fecha_creacion', 'fecha_edicion', 'fecha_borrado'], 'safe'],
            [['nombre', 'cuenta_contable'], 'string', 'max' => 255],
            [['cuenta_contable'], 'unique'],
            [['nombre'], 'unique'],
            [['rendicion_categoria_id'], 'exist', 'skipOnError' => true, 'targetClass' => RendicionCategoria::className(), 'targetAttribute' => ['rendicion_categoria_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nombre' => 'Nombre',
            'rendicion_categoria_id' => 'Rendicion Categoria ID',
            'fecha_creacion' => 'Fecha Creacion',
            'fecha_edicion' => 'Fecha Edicion',
            'fecha_borrado' => 'Fecha Borrado',
            'cuenta_contable' => 'Cuenta Contable',
        ];
    }

    /**
     * Gets query for [[RendicionDetalles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRendicionDetalles()
    {
        return $this->hasMany(RendicionDetalle::className(), ['motivo_rendicion_id' => 'id']);
    }

    /**
     * Gets query for [[RendicionCategoria]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRendicionCategoria()
    {
        return $this->hasOne(RendicionCategoria::className(), ['id' => 'rendicion_categoria_id']);
    }
}

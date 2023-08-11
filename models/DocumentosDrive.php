<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "documentos_drive".
 *
 * @property int $id
 * @property int $id_usuario
 * @property string $id_drive
 * @property string $nombre_archivo_drive
 *
 * @property Usuarios $usuario
 */
class DocumentosDrive extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'documentos_drive';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_usuario'], 'default', 'value' => null],
            [['id_usuario'], 'integer'],
            [['id_drive', 'nombre_archivo_drive'], 'required'],
            [['id_drive', 'nombre_archivo_drive'], 'string', 'max' => 255],
            [['id_drive'], 'unique'],
            [['id_usuario'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['id_usuario' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_usuario' => 'Id Usuario',
            'id_drive' => 'Id Drive',
            'nombre_archivo_drive' => 'Nombre Archivo Drive',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'id_usuario']);
    }
}

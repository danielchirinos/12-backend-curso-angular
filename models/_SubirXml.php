<?php
namespace app\models;

use yii\base\Model;

class _SubirXml extends Model{

    public $archivo;

    public function rules()
    {
        return [
            [['archivo'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xml'],
        ];
    }
    
    // public function upload(){
    //     if ($this->validate()) {
    //         $this->imageFile->saveAs('uploads/' . $this->imageFile->baseName . '.' . $this->imageFile->extension);
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }
}
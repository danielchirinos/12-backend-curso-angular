<?php

return [
    // Configuración de la base de datos secundaria
    'class' => 'yii\db\Connection',
    'dsn' => 'pgsql:host=192.168.101.92;port=6432;dbname=tms_interandinos_qa',
    'username' => 'tms_interandinos',
    'password' => 'tms_interandinos',
    'charset' => 'utf8',
    'emulatePrepare' => 1, 
];
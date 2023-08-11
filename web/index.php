<?php

// comment out the following two lines when deployed to production
defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
if(getenv('API-MAPS') != false){
    define('MAPS_API_KEY',getenv('API-MAPS'));
}else{
    define('MAPS_API_KEY',"AIzaSyA27FixtDsBpAocUYZL8wkbGUwlAp_fUDc");
}

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../vendor/yiisoft/yii2/Yii.php';

$config = require __DIR__ . '/../config/web.php';

(new yii\web\Application($config))->run();

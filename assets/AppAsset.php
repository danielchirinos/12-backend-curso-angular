<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\View;
use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'content/bootstrap-4.4.1/css/bootstrap.min.css',
    ];
    public $js = [
        'content/bootstrap-4.4.1/js/bootstrap.bundle.min.js',
        // 'content/bootstrap-4.4.1/js/popper.min.js',
    ];
    public $jsOptions = ['position' => View::POS_HEAD];
    public $depends = [
        'yii\web\YiiAsset',
        // 'yii\bootstrap\BootstrapAsset',
    ];
}

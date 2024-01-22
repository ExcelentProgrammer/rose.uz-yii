<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class PanelAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'https://fonts.googleapis.com/css?family=Open+Sans:400,600',
        'assets/panel/css/animate.min.css',
        "assets/panel/js/bootstrap-notify.js",
        'assets/panel/css/light-bootstrap-dashboard.css?v=1.4.1',
        'font-awesome/css/font-awesome.min.css',
        'assets/panel/css/pe-icon-7-stroke.css',
        'assets/panel/css/demo.css?v=1.0.4',
    ];
    public $js = [
        'assets/panel/js/chartjs.min.js',
        'assets/panel/js/bootstrap-notify.js',
        'assets/panel/js/light-bootstrap-dashboard.js?v=1.4.1',
        'assets/panel/js/demo.js?v=1.0.1',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_END
    ];
}

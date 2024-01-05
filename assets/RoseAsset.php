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
class RoseAsset extends AssetBundle
{
  public $basePath = '@webroot';
  public $baseUrl = '@web';
  public $css = [
    'css/font-awesome.min.css',
    'css/bootstrap-datepicker.min.css',
    'css/layerslider.css',
    'css/style.css?v=1.0.3',
  ];
  public $js = [
    'js/jquery.inputmask.bundle.js',
    'js/bootstrap-datepicker.min.js',
    'js/greensock.js',
    'js/layerslider.transitions.js',
    'js/layerslider.kreaturamedia.jquery.js',
    'js/script.js?v=1.0.1'
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

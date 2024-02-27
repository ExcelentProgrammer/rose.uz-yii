<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ru-RU',
    'sourceLanguage' => 'ru-RU',
    'layout' => 'rose',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'hm1y40MdnjCpguF1eP5GJfp5S_3NEPi8',
        ],

        'urlManager' => [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'web' => 'site/index',
                'language/<lang:\w+>' => 'site/language',
                'currency/<id:\d+>' => 'site/set-currency',
                '/login' => '/panel/default/login',
                '/logout' => '/panel/default/logout',
                '/offer' => '/site/offer',
                'catalog/<id:\d+>' => 'catalog/view',
                'pages/<url:\w+>' => 'pages/view',
                'state' => 'pages/state',
                'cart' => 'catalog/cart',
                'checkout' => 'catalog/checkout',
                'payment' => 'catalog/payment',
                'click-prepare' => 'payment/click-prepare',
                'click-complete' => 'payment/click-complete',
                "octo-check" => "octo/check",
                'pay/<id:\d+>/<system:[a-z-]+>' => 'catalog/pay',
            ]
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['/panel/default/login'],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'session' => [
            'class' => 'yii\web\DbSession',
        ],
        'db' => require(__DIR__ . '/db.php'),
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'basePath' => '@webroot',
                    'baseUrl' => '@web',
                    'js' => ['js/jquery.min.js']
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'basePath' => '@webroot',
                    'baseUrl' => '@web',
                    'js' => ['js/bootstrap.min.js']
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'basePath' => '@webroot',
                    'baseUrl' => '@web',
                    'css' => ['css/bootstrap.min.css'],
//                    'js' => ['js/bootstrap.min.js']
                ],
            ],
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'fileMap' => [
                        'app' => 'app.php',
                    ],
                ],
            ],
        ],
    ],
    'modules' => [
        'panel' => [
            'class' => 'app\modules\panel\Module',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['83.221.172.5', '84.54.123.95'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['83.221.172.5', '84.54.123.95'],
    ];
}

return $config;
<?php

namespace app\modules\panel;

/**
 * panel module definition class
 */
class Module extends \yii\base\Module
{
    public $layout = 'main';
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\panel\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        // custom initialization code goes here
        \Yii::$app->errorHandler->errorAction = '/panel/default/error';
    }
}

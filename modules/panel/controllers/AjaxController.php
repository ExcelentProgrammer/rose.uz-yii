<?php

namespace app\modules\panel\controllers;

use app\components\AccessRule;
use app\models\Orders;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;

/**
 * BotsController implements the CRUD actions for Bots model.
 */
class AjaxController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['*'],
                'rules' => [
                    [
                        'actions' => ['change-state'],
                        'allow' => true,
                        'roles' => ["@"],
                    ],
                ],
            ],
        ];
    }

    public function actionChangeState()
    {
        $response = [];
        Yii::$app->response->format = Response::FORMAT_JSON;

        $id = Yii::$app->request->post('id');
        $state = Yii::$app->request->post('state');

        $order = Orders::findOne($id);
        if ($order === null) {
            $response['state'] = "Error";
            return $response;
        }

        $order->state = $state;
        $order->save();

        $response['state'] = "OK";
        return $response;
    }
}

<?php

namespace app\controllers;

use app\models\Orders;
use app\Services\OctoService;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

class OctoController extends Controller
{
    public $service;

    public function __construct($id, $module, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = new OctoService();
    }

    public function beforeAction($action)
    {
        $lang = Yii::$app->request->cookies->get('language');
        if (empty($lang)) {
            $lang = "ru";
        }
        Yii::$app->language = $lang;

        $this->enableCsrfValidation = false;
        \Yii::$app->response->format = Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    function actionComplete()
    {
        Yii::$app->response->format = Response::FORMAT_HTML;
        return $this->render('complete');
    }

    function actionCheck()
    {
        $data = json_decode(Yii::$app->request->getRawBody());
        if ($data->status == "succeeded") {
            $order = Orders::findOne(['id' => $data->shop_transaction_id]);
            $amount = $data->total_sum;
            $this->service->completeOrder($order, $amount);
            return [
                'accept_status' => 'capture'
            ];
        }
        return [
            "accept_status" => "cancel"
        ];
    }

    function actionCreate()
    {
        try {
            $model = $this->service->getOrder();
        } catch (\Exception $e) {
            if ($e->getCode() == 1001) {
                return $this->redirect(['checkout']);
            }
        }

        $res = $this->service->createPaymentUrl($model->getOrderTotalSumPrice(), $model->id);
        if ($res->status == 'created') {
            $url = $res->octo_pay_url;
            return $this->redirect($url);
        } elseif ($res->status == "succeeded") {
            return $this->redirect(Url::to("/octo/complete"));
        } else {
            return $this->redirect(Yii::$app->request->referrer);
        }

    }
}
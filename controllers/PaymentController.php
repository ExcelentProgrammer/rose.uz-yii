<?php

namespace app\controllers;

use app\models\Dashboard;
use app\models\TelegramBot;
use app\models\Click;
use app\models\ClickPayments;
use app\models\Orders;
use app\models\Payments;
use app\models\PaymeTransaction;
use app\models\PaynetTransaction;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class PaymentController extends Controller
{
    public function beforeAction($action)
    {
        $lang = Yii::$app->request->cookies->get('language');
        if (empty($lang)) {
            $lang = "ru";
        }
        Yii::$app->language = $lang;
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionClick($id)
    {
        $this->layout = "clear";
        return $this->render("click", [
            'model' => $this->findModel($id)
        ]);
    }

    public function actionClickPrepare()
    {
        $click_trans_id = Yii::$app->request->post('click_trans_id');
        $service_id = Yii::$app->request->post('service_id');
        $merchant_trans_id = Yii::$app->request->post('merchant_trans_id');
        $amount = Yii::$app->request->post('amount');
        $action = Yii::$app->request->post('action');
        $error = Yii::$app->request->post('error');
        $error_note = Yii::$app->request->post('error_note');
        $sign_time = Yii::$app->request->post('sign_time');
        $sign_string = Yii::$app->request->post('sign_string');
        $click_paydoc_id = Yii::$app->request->post('click_paydoc_id');

        $this->log(serialize($_POST));

        /*Check SIGN*/
        $sign_string_veryfied = md5(
            $click_trans_id .
            $service_id .
            Click::SECRET_KEY .
            $merchant_trans_id .
            $amount .
            $action .
            $sign_time); // Формирование ХЭШ подписи

        if ($sign_string_veryfied != $sign_string) {
            $this->send(Click::$messages[1]);
            return null;
        }

        if (!in_array($action, [0, 1])) {
            $this->send(Click::$messages[3]);
            return null;
        }

        $order = Orders::findOne(['id' => $merchant_trans_id, 'is_paid' => 0]);
        if ($order === null) {
            $this->send(Click::$messages[5]);
            return null;
        }

//        if ($order->getOrderTotalPrice(true) != $amount) {
//            $this->send(Click::$messages[2]);
//            return null;
//        }

        $model = new ClickPayments();
        $model->order_id = $order->id;
        $model->click_trans_id = $click_trans_id;
        $model->service_id = $service_id;
        $model->click_paydoc_id = $click_paydoc_id;
        $model->merchant_trans_id = $merchant_trans_id;
        $model->amount = $amount;
        $model->action = $action;
        $model->error = $error;
        $model->error_note = $error_note;
        $model->sign_time = $sign_time;
        $model->sign_string = $sign_string;
        if ($model->save()) {
            $response['click_trans_id'] = $click_trans_id;
            $response['merchant_trans_id'] = $merchant_trans_id;
            $response['merchant_prepare_id'] = $model->id;
            $response['error'] = 0;
            $response['error_note'] = "success";
            $this->send($response);
        } else {
            $this->log(reset($model->firstErrors));
        }
        return null;
    }

    public function actionClickComplete()
    {
        $click_trans_id = Yii::$app->request->post('click_trans_id');
        $service_id = Yii::$app->request->post('service_id');
        $merchant_trans_id = Yii::$app->request->post('merchant_trans_id');
        $merchant_prepare_id = Yii::$app->request->post('merchant_prepare_id');
        $amount = Yii::$app->request->post('amount');
        $action = Yii::$app->request->post('action');
        $error = Yii::$app->request->post('error');
        $error_note = Yii::$app->request->post('error_note');
        $sign_time = Yii::$app->request->post('sign_time');
        $sign_string = Yii::$app->request->post('sign_string');
        $click_paydoc_id = Yii::$app->request->post('click_paydoc_id');

        if (empty($click_trans_id) || empty($service_id) || empty($merchant_trans_id) || empty($merchant_prepare_id) || empty($amount) || empty($action) || empty($error_note) || empty($sign_time) || empty($sign_string) || empty($click_paydoc_id)) {
            $this->send(Click::$messages[8]);
            return null;
        }

        $this->log(serialize($_POST));

        /*Check SIGN*/
        $sign_string_veryfied = md5(
            $click_trans_id .
            $service_id .
            Click::SECRET_KEY .
            $merchant_trans_id .
            $merchant_prepare_id .
            $amount .
            $action .
            $sign_time); // Формирование ХЭШ подписи


        if ($sign_string_veryfied != $sign_string) {
            $this->send(Click::$messages[1]);
            return null;
        }

        if (!in_array($action, [0, 1])) {
            $this->send(Click::$messages[3]);
            return null;
        }

        $model = ClickPayments::findOne($merchant_prepare_id);
        if ($model == null) {
            $this->send(Click::$messages[6]);
            return null;
        }

        if ($model->action == 1 && $model->error == 0) {
            $this->send(Click::$messages[4]);
            return null;
        }

        if ($model->action == ClickPayments::STATE_CANCELED && $model->error == -5017) {
            $this->send(Click::$messages[9]);
            return null;
        }

        if ($model->amount != $amount) {
            $this->send(Click::$messages[2]);
            return null;
        }

        if ((int)$error == -5017) {
            $model->error = $error;
            $model->action = ClickPayments::STATE_CANCELED;
            $model->save();
            $this->send(Click::$messages[9]);
            return null;
        }

        $model->action = $action;
        $model->error = $error;
        $model->error_note = $error_note;
        $model->sign_time = $sign_time;
        $model->sign_string = $sign_string;
        $model->save();

        if ($model->action == 1 && $model->error == 0) {
            $response['click_trans_id'] = $click_trans_id;
            $response['merchant_trans_id'] = $merchant_trans_id;
            $response['merchant_confirm_id'] = $model->id;
            $response['error'] = 0;
            $response['error_note'] = "success";
            $this->send($response);

            $payment = new Payments();
            $payment->order_id = $model->order_id;
            $payment->type = 1;
            $payment->amount = $model->amount;
            $payment->service = "CLICK_PAYMENT";
            $payment->save();

            $order = Orders::findOne($model->order_id);
            $order->state = Orders::STATE_PAID;
            $order->is_paid = 1;
            $order->payment_type = Orders::PAYMENT_CLICK;
            $order->total_paid = $payment->amount;
            $order->save();

            $amountText = number_format($payment->amount, 0, '.', ' ');
            Dashboard::sendNotification("Rose.uz: Оплата через Click. ID заказа: {$model->order_id}. Сумма: {$amountText} сум");
        }

        return null;
    }

    public function actionPayme()
    {
        $request_body = file_get_contents('php://input');
        new PaymeTransaction($request_body);
        return null;
    }

    public function actionPaynet()
    {
        $input = file_get_contents('php://input');
        $this->log($input);
        $paynet = new PaynetTransaction($input);
//        print_r($paynet);
        echo $paynet->response;
    }

    public function actionUpay()
    {
        $result = [];
        Yii::$app->response->format = Response::FORMAT_JSON;

        $hash = "a90e60c02006eae16c8804e45626d97b3071cb9b";
        $upayTransId = Yii::$app->request->post('upayTransId');
        $upayTransTime = Yii::$app->request->post('upayTransTime');
        $upayPaymentAmount = Yii::$app->request->post('upayPaymentAmount');
        $personalAccount = Yii::$app->request->post('personalAccount');
        $accessToken = Yii::$app->request->post('accessToken');

        if ($hash !== $accessToken) {
            $result['status'] = 0;
            $result['message'] = 'Ошибка подлиности';
            return $result;
        }

        $model = Orders::find()->where(['id' => $personalAccount, 'state' => Orders::STATE_CREATED])->one();
        if ($model === null) {
            $result['status'] = 0;
            $result['message'] = 'Заказ с таким номером не найден.';
            return $result;
        }

        $result['status'] = 1;
        $result['message'] = 'Успешно';
        return $result;

    }

    public function actionRedirect($order_id, $system)
    {
        $this->layout = 'clear';
        $order = Orders::find()->where(['id' => $order_id, 'state' => Orders::STATE_CREATED])->one();
        if ($order == null) {
            echo "Такой заказ не найден!";
            return false;
        }

        if (empty($system)) {
            echo "Не определенная система оплаты!";
            return false;
        }
        return $this->render('redirect', [
            'model' => $order,
            'system' => $system
        ]);
    }

    private function send($data)
    {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data);
    }

    private function log($data)
    {
        $fp = fopen('data.txt', 'w');
        fwrite($fp, "{$data}" . PHP_EOL);
        fclose($fp);
    }

    /**
     * Finds the Orders model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Orders the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Orders::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Страница не найдена.');
    }
}

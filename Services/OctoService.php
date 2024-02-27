<?php

namespace app\Services;

use app\models\Dashboard;
use app\models\Orders;
use app\models\Payments;
use yii\web\NotFoundHttpException;

class OctoService
{
    public $token = "92d681b5-4291-43b4-b609-24d874ab4fab";
    public $shop_id = 6224;

    public function request($method, $url, $data)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        return json_decode($response);

    }

    function createPaymentUrl($amount, $trans_id)
    {
        $data = array(
            "octo_shop_id" => $this->shop_id,
            "octo_secret" => $this->token,
            "shop_transaction_id" => $trans_id,
            "auto_capture" => true,
            "test" => false,
            "init_time" => "2018-03-30 11:22:33",
            "total_sum" => $amount,
            "currency" => "UZS",
            "tag" => "ticket",
            "description" => "Название платежа, например: Авиаперелет Москва-Сингапур",
            "payment_methods" => array(
                array("method" => "bank_card")
            ),
            "tsp_id" => 18,
            "return_url" => "https://rose.uz/octo/complete/",
            "language" => "uz",
            "ttl" => 15
        );
        $response = $this->request("POST", "https://secure.octo.uz/prepare_payment", $data);
        return $response;
    }

    function findOrderModel($id)
    {

//      'system_id' => Yii::$app->params['system']
        if (($model = Orders::findOne(['id' => $id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не существует.');
        }
    }

    function getOrder()
    {
        $cacheOrder = Orders::getCachedOrder();
        if ($cacheOrder['state'] < 3) {
            throw new \Exception("checkout", 1001);
        }
        $model = $this->findOrderModel($cacheOrder['id']);
        if ($model->load(\Yii::$app->request->post())) {
            $model->save();
        }
        return $model;
    }

    function completeOrder($model, $amount)
    {
        $payment = new Payments();
        $payment->order_id = $model->id;
        $payment->type = 1;
        $payment->amount = $amount;
        $payment->service = "OCTO_PAYMENT";
        $payment->save();

        $order = Orders::findOne($model->id);
        $order->state = Orders::STATE_PAID;
        $order->is_paid = 1;
        $order->payment_type = Orders::PAYMENT_OCTO;
        $order->total_paid = $amount;
        $order->save();

        $amountText = number_format($payment->amount, 0, '.', ' ');
        Dashboard::sendNotification("Rose.uz: Оплата через Octo visa/mastercard. ID заказа: {$model->id}. Сумма: {$amountText} сум");

    }

}
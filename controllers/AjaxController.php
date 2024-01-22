<?php

namespace app\controllers;

use app\models\Bot;
use app\models\Dashboard;
use app\models\Orders;
use app\models\Products;
use app\models\Rose;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Response;

class AjaxController extends Controller
{
    public function beforeAction($action)
    {
        $lang = Yii::$app->request->cookies->get('language');
        if (empty($lang)) {
            $lang = "ru";
        }
        Yii::$app->language = $lang;
        return parent::beforeAction($action);
    }

    public function actionAddToCart()
    {
        $response = [];
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->post('id');

        $product = Products::findOne($id);
        if ($product == null) {
            $response['state'] = "Error";
            $response['message'] = "Product not found";
            return $response;
        }

        $quantity = 1;
        $cachedOrder = Orders::getCachedOrder();
        $productsId = ArrayHelper::getColumn($cachedOrder['items'], 'id');
        if (!in_array($product->id, $productsId)) {
            $item = [
                'id' => $product->id,
                'quantity' => $quantity,
            ];
            $cachedOrder['items'][] = $item;

            Orders::setCachedOrder($cachedOrder);
        }
        $response['state'] = "OK";
        $response['alert'] = '<div class="alert-area"><div class="alert-success p10">Продукт <b>' . $product->name . '</b> добавлен в корзину</div></div>';
        $response['count'] = count($cachedOrder['items']);
        $response['total'] = Orders::getCachedOrderTotalPrice($cachedOrder);

        return $response;
    }

    public function actionMinusQuantity()
    {
        $response = [];
        Yii::$app->response->format = Response::FORMAT_JSON;

        $itemId = Yii::$app->request->post('itemId');

        $quantity = 0;
        $itemPrice = 0;
        $cachedOrder = Orders::getCachedOrder();
        foreach ($cachedOrder['items'] as $key => $item) {
            if ($item['id'] == $itemId) {
                $cachedOrder['items'][$key]['quantity']--;
                $quantity = $cachedOrder['items'][$key]['quantity'];
                $product = Products::findOne($cachedOrder['items'][$key]['id']);
                $itemPrice = Orders::getAbsolutePrice($product->price * $quantity);
            }
        }
        Orders::setCachedOrder($cachedOrder);
        $response['state'] = "OK";
        $response['quantity'] = $quantity;
        $response['itemPrice'] = $itemPrice;
        $response['total'] = Orders::getCachedOrderTotalPrice($cachedOrder);
        return $response;
    }

    public function actionPlusQuantity()
    {
        $response = [];
        Yii::$app->response->format = Response::FORMAT_JSON;

        $itemId = Yii::$app->request->post('itemId');

        $quantity = 0;
        $itemPrice = 0;
        $cachedOrder = Orders::getCachedOrder();
        foreach ($cachedOrder['items'] as $key => $item) {
            if ($item['id'] == $itemId) {
                $cachedOrder['items'][$key]['quantity']++;
                $quantity = $cachedOrder['items'][$key]['quantity'];
                $product = Products::findOne($cachedOrder['items'][$key]['id']);
                $itemPrice = Orders::getAbsolutePrice($product->price * $quantity);
            }
        }
        Orders::setCachedOrder($cachedOrder);
        $response['state'] = "OK";
        $response['quantity'] = $quantity;
        $response['itemPrice'] = $itemPrice;
        $response['total'] = Orders::getCachedOrderTotalPrice($cachedOrder);
        return $response;
    }

    public function actionSetQuantity()
    {
        $response = [];
        Yii::$app->response->format = Response::FORMAT_JSON;

        $itemId = Yii::$app->request->post('itemId');
        $quantity = Yii::$app->request->post('quantity');

        $itemPrice = 0;
        $cachedOrder = Orders::getCachedOrder();
        foreach ($cachedOrder['items'] as $key => $item) {
            if ($item['id'] == $itemId) {
                $cachedOrder['items'][$key]['quantity'] = $quantity;
                $product = Products::findOne($cachedOrder['items'][$key]['id']);
                $itemPrice = Orders::getAbsolutePrice($product->price * $quantity);
            }
        }
        Orders::setCachedOrder($cachedOrder);
        $response['state'] = "OK";
        $response['quantity'] = $quantity;
        $response['itemPrice'] = $itemPrice;
        $response['total'] = Orders::getCachedOrderTotalPrice($cachedOrder);
        return $response;
    }

    public function actionDeleteItem()
    {
        $response = [];
        Yii::$app->response->format = Response::FORMAT_JSON;

        $itemId = Yii::$app->request->post('itemId');

        $cachedOrder = Orders::getCachedOrder();
        foreach ($cachedOrder['items'] as $key => $item) {
            if ($item['id'] == $itemId) {
                unset($cachedOrder['items'][$key]);
            }
        }
        Orders::setCachedOrder($cachedOrder);
        $response['state'] = "OK";
        $response['total'] = Orders::getCachedOrderTotalPrice($cachedOrder);
        return $response;
    }

    public function actionSendCallback()
    {
        $response = [];
        Yii::$app->response->format = Response::FORMAT_JSON;

        $number = Yii::$app->request->post('number');
        Dashboard::sendNotification("Rose.uz: Позвонить на номер: " . $number);

        $response['state'] = "OK";
        return $response;
    }

    public function actionPayboxComplete()
    {
        $response = [];
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $amount = $request->post('amount');
        $invoiceId = $request->post('invoiceId');
        $invoiceId = $request->post('invoiceId');
        $currency = $request->post('currency');

        $order = Orders::findOne($invoiceId); // Order

        $order->state = Orders::STATE_PAID;
        $order->is_paid = 1;
        $order->payment_type = Orders::PAYMENT_CLOUDPAYMENTS;
        $order->total_paid = $amount;
        $order->save();

        $order = $request->post('order');
        $amount = number_format($amount, 2);
        Dashboard::sendNotification("Rose.uz: Оплата через CLOUDPAYMENTS. ID заказа: {$order->id}. Сумма: {$amount} сум");
        $response['state'] = "OK";
        return $response;
    }

}

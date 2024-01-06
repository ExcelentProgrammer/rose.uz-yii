<?php

namespace app\controllers;

use app\models\Category;
use app\models\Dashboard;
use app\models\OrderItems;
use app\models\Orders;
use app\models\Products;
use app\models\TelegramBot;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class CatalogController extends Controller
{


    public $enableCsrfValidation = false;

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // remove authentication filter
        unset($behaviors['authenticator']);

        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['POST', 'GET', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Allow-Headers' => ['Origin', 'X-Requested-With', 'Content-Type', 'accept', 'Authorization'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Max-Age' => 3600, // Cache (seconds)
                // Allow the X-Pagination-Current-Page header to be exposed to the browser.             
                'Access-Control-Expose-Headers' => ['X-Pagination-Total-Count', 'X-Pagination-Page-Count', 'X-Pagination-Current-Page', 'X-Pagination-Per-Page']
            ]
        ];

        $behaviors['authenticator'] = [];

        $behaviors['access'] = [
            'class' => AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['options'], // important for cors ie. pre-flight requests   
                ],
            ]
        ];
    }

    public function beforeAction($action)
    {
        $lang = Yii::$app->request->cookies->get('language');
        if (empty($lang)) {
            $lang = "ru";
        }
        Yii::$app->language = $lang;
        return parent::beforeAction($action);
    }


    /**
     * Lists all Category models.
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Displays a single Category model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionCart()
    {
        $cacheOrder = Orders::getCachedOrder();
        $model = new Orders();
        if ($model->load(Yii::$app->request->post())) {
            $order = Yii::$app->request->post("Orders");
            $model->add_card = !empty($order['add_card']) ? 1 : 0;
            $model->take_photo = !empty($order['take_photo']) ? 1 : 0;
            $cacheOrder['state'] = 2;
            $cacheOrder['add_card'] = $model->add_card;
            $cacheOrder['take_photo'] = $model->take_photo;
            $cacheOrder['card_text'] = trim($model->card_text);
            Orders::setCachedOrder($cacheOrder);
            return $this->redirect(['checkout']);
        }

        return $this->render('cart', [
            'model' => $model
        ]);
    }

    public function actionCheckout()
    {
        $cacheOrder = Orders::getCachedOrder();

        if ($cacheOrder['state'] < 2) {
            return $this->redirect(['cart']);
        }


        $model = new Orders();


        if (!empty($cacheOrder['id']))
            $model = $this->findOrderModel($cacheOrder['id']);


        if ($model->load(Yii::$app->request->post())) {
            $order = Yii::$app->request->post("Orders");
            $model->date = date("Y-m-d H:i");
            $model->add_card = !empty($order['add_card']) ? 1 : 0;
            $model->take_photo = !empty($order['take_photo']) ? 1 : 0;

            $cacheOrder['sender_name'] = $model->sender_name;
            $cacheOrder['sender_phone'] = $model->sender_phone;
            $cacheOrder['sender_email'] = $model->sender_email;
            $cacheOrder['receiver_name'] = $model->receiver_name;
            $cacheOrder['receiver_phone'] = $model->receiver_phone;

            $cacheOrder['receiver_address'] = $model->receiver_address;
            $cacheOrder['know_address'] = $model->know_address;
            $cacheOrder['add_card'] = $model->add_card;
            $cacheOrder['take_photo'] = $model->take_photo;
            $cacheOrder['card_text'] = trim($model->card_text);
            if (!empty($model->delivery_date)) {
                $model->delivery_date = date("Y-m-d", strtotime($model->delivery_date));
                $cacheOrder['delivery_date'] = $model->delivery_date;
            }
            $cacheOrder['delivery_period'] = $model->delivery_period;

//                TelegramBot::sendNotification("Rose.uz: Новый заказ оформлено. ID заказа: " . $model->id);
            $model->state = Orders::STATE_CREATED;
            $model->system_id = $cacheOrder['system_id'];
            $model->client_type = $cacheOrder['client_type'];
            $model->access_token = Yii::$app->security->generateRandomString(64);
            if ($model->save()) {
                $t = 0;
                OrderItems::deleteAll(['order_id' => $model->id]);
                foreach ($cacheOrder['items'] as $item) {
                    $product = Products::findOne($item['id']);
                    $orderItem = new OrderItems();
                    $orderItem->order_id = $model->id;
                    $orderItem->product_id = $product->id;
                    $orderItem->product_name = $product->name;
                    $orderItem->amount = $item['quantity'];
                    $orderItem->price = $product->price;
                    $orderItem->save();
                    $t += $orderItem->amount * $item->price;
                }
                $model->total = $t;
                $model->save();
                $cacheOrder['id'] = $model->id;
                $cacheOrder['state'] = 3;
                Orders::setCachedOrder($cacheOrder);
                if (empty($cacheOrder['id']))
                    Dashboard::sendNotification("Поступил новый заказ. Пожалуйста проверьте админку! Номер заказа: {$model->id}");
                $bot = new TelegramBot();
                $bot->sendMessage(101361497, "Yangi buyurtma 📍\n\nBuyurtma: <a href='https://rose.uz/panel/orders/view?id=" . $cacheOrder['id'] . "\n\n'>#".$cacheOrder['id']."</a>", "html");
                $bot->sendMessage(1769851684, "Yangi buyurtma 📍\n\nBuyurtma: <a href='https://rose.uz/panel/orders/view?id=" . $cacheOrder['id'] . "\n\n'>#".$cacheOrder['id']."</a>", "html");

                return $this->redirect(['payment']);
            }
        }
        return $this->render('checkout', [
            'model' => $model
        ]);
    }

    public function actionPayment()
    {
        $header = header('Access-Control-Allow-Origin: *');

        $cacheOrder = Orders::getCachedOrder();
        if ($cacheOrder['state'] < 3) {
            return $this->redirect(['checkout']);
        }
        $model = $this->findOrderModel($cacheOrder['id']);
        if ($model->load(Yii::$app->request->post())) {
            $model->save();
        }
        return $this->render('payment', [
            'model' => $model
        ]);
    }

    public function actionPay($id, $system)
    {
        $model = Orders::findOne(['id' => $id, 'state' => 0]);
        if ($model == null)
            throw new NotFoundHttpException('Запрашиваемая страница не существует.');

        return $this->render('pay', [
            'model' => $model,
            'system' => $system,
        ]);
    }

    public function actionComplete()
    {
        return $this->render('complete');
    }

    /**
     * Finds the Category model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Category the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Category::findOne(['id' => $id, 'bot_id' => Yii::$app->params['system']])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не существует.');
        }
    }

    protected function findOrderModel($id)
    {

//      'system_id' => Yii::$app->params['system']
        if (($model = Orders::findOne(['id' => $id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Запрашиваемая страница не существует.');
        }
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orders".
 *
 * @property integer $id
 * @property integer $chat_id
 * @property string $client_type
 * @property integer $system_id
 * @property string $payment_type
 * @property string $date
 * @property string $sender_name
 * @property string $sender_phone
 * @property string $sender_email
 * @property string $receiver_name
 * @property string $receiver_phone
 * @property string $delivery_date
 * @property double $delivery_price
 * @property double $total_paid
 * @property string $receiver_address
 * @property integer $know_address
 * @property integer $add_card
 * @property integer $take_photo
 * @property string $card_text
 * @property string $total
 * @property integer $state
 * @property integer $is_paid
 *
 * @property OrderItems[] $items
 * @property Bots $system
 */
class Orders extends \yii\db\ActiveRecord
{
    const STATE_TRASH = -1;
    const STATE_CREATED = 0;
    const STATE_PAID = 1;
    const STATE_ACCEPTED = 2;
    const STATE_PROCESS = 3;
    const STATE_SENT = 4;
    const STATE_RECEIVED = 5;
    const STATE_CANCELED = 6;

    const CLIENT_BOT = "bot";
    const CLIENT_WEB = "web";

    const PAYMENT_PAYME = "PAYME";
    const PAYMENT_CLICK = "CLICK";
    const PAYMENT_PAYNET = "PAYNET";
    const PAYMENT_OCTO = "OCTO";

    const PAYMENT_CLOUDPAYMENTS = "CLOUDPAYMENTS";
    const PAYMENT_YANDEX = "YANDEX";
    const PAYMENT_WEB_MONEY = "WEB_MONEY";
    const PAYMENT_CASH = "CASH";

    const CUR_SUM = 1;
    const CUR_RUB = 2;
    const CUR_USD = 3;

    const RUB_RATE = 105;
    const USD_RATE = 9000;

    public static $currency = [
        1 => "СУМ",
        2 => "РУБ",
        3 => "USD"
    ];

    public static $clientTypes = ['bot' => "Бот", 'web' => 'Сайт'];
    public static $statuses = [
        "Создано",
        "Оплачено",
        "Принят",
        "Собирается",
        "Отправлено",
        "Доставлено",
        "Отменено",
    ];

    public static $period = [
        'day' => 'В течение дня',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sender_name', 'sender_phone', 'receiver_name', 'receiver_phone', 'delivery_date'], 'required', 'on' => "checkout"],
            [['chat_id', 'system_id', 'know_address', 'add_card', 'take_photo', 'state', 'is_paid'], 'integer'],
            [['delivery_price', 'total_paid'], 'number'],
            [['date', 'delivery_date', 'total'], 'safe'],
            [['receiver_address', 'card_text'], 'string'],
            [['sender_name', 'sender_email', 'receiver_name', 'payment_type'], 'string', 'max' => 50],
            [['sender_phone', 'receiver_phone'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Номер заказа',
            'chat_id' => 'Чат',
            'client_type' => 'Тип клиента',
            'system_id' => 'Система',
            'payment_type' => 'Платежная система',
            'date' => 'Создано в',
            'sender_name' => 'Имя отправителя',
            'sender_phone' => 'Номер отправителя',
            'sender_email' => 'E-mail отправителя',
            'receiver_name' => 'Имя получателя',
            'receiver_phone' => 'Номер получателя',
            'delivery_date' => 'Дата доставки',
            'receiver_address' => 'Адрес доставки',
            'know_address' => 'Узнать адрес',
            'add_card' => 'Открытка',
            'take_photo' => 'Фото',
            'card_text' => 'Текст открытки',
            'total' => 'Итого',
            'state' => 'Статус',
            'total_paid' => 'Оплачено',
            'is_paid' => 'Оплачено',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(OrderItems::className(), ['order_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSystem()
    {
        return $this->hasOne(Bots::className(), ['id' => 'system_id']);
    }

    /**
     * @param TelegramChat $chat
     * @return Orders
     */
    public static function getOrder($chat)
    {
        $model = self::find()->where(['chat_id' => $chat->id, 'client_type' => self::CLIENT_BOT, 'state' => self::STATE_CREATED])->one();
        if ($model == null) {
            $model = new Orders();
            $model->chat_id = $chat->id;
            $model->client_type = self::CLIENT_BOT;
            $model->date = date("Y-m-d H:i:s");
            $model->sender_name = trim($chat->first_name . " " . $chat->last_name);
            $model->sender_phone = $chat->getPhone();
            $model->state = self::STATE_CREATED;
            $model->system_id = Yii::$app->params['system'];
            $model->save();
        }

        return $model;
    }

    /**
     * @param Products $product
     * @return boolean
     */
    public function addItem($product)
    {
        $model = OrderItems::find()->where(['order_id' => $this->id, 'product_id' => $product->id])->one();
        if ($model == null) {
            $model = new OrderItems();
            $model->order_id = $this->id;
            $model->product_id = $product->id;
        }
        $model->product_name = $product->name;
        $model->amount = 1;
        $model->price = $product->price;
        if ($model->save())
            return true;

        return false;
    }

    /**
     * @param integer $chat_id
     * @return Orders
     */
    public static function getCart($chat_id)
    {
        return self::find()->where(['chat_id' => $chat_id, 'client_type' => self::CLIENT_BOT, 'state' => self::STATE_CREATED, 'system_id' => Yii::$app->params['system']])->one();
    }

    public function getItemsCount()
    {
        return count($this->items);
    }

    /**
     * Calculates the absolute price based on the given value and the currency rate.
     * @param float $value the value to be converted to absolute price
     * @return float the absolute price
     */
    public static function getAbsolutePrice($value)
    {
        $currency = self::getCurrency();
        if ($currency == self::CUR_RUB) {
            return $value * self::RUB_RATE;
        } elseif ($currency == self::CUR_USD) {
            return $value * self::USD_RATE;
        } else {
            return $value;
        }
    }

    /**
     * Retrieves the cached order.
     * @return array the cached order data
     */
    public static function getCachedOrder()
    {
        $session = Yii::$app->session;

        if (!$session->has('cachedOrder')) {
            $session->set('cachedOrder', [
                'items' => [],
                'total' => 0,
            ]);
        }

        return $session->get('cachedOrder');
    }

    /**
     * Sets the cached order.
     * @param array $order the order data to be cached
     */
    public static function setCachedOrder($order)
    {
        $session = Yii::$app->session;
        $session->set('cachedOrder', $order);
    }

    /**
     * Calculates and returns the total price of the cached order.
     * @param array|null $cachedOrder the cached order data. Defaults to `null`,
     * meaning the current cached order will be used.
     * @return float the total price of the cached order
     */
    public static function getCachedOrderTotalPrice($cachedOrder = null)
    {
        if ($cachedOrder === null) {
            $cachedOrder = self::getCachedOrder();
        }

        $total = 0;
        foreach ($cachedOrder['items'] as $item) {
            $product = Products::findOne($item['id']);
            if ($product !== null) {
                $total += $product->getPrice() * $item['quantity'];
            }
        }

        return $total;
    }

    /**
     * Retrieves the cached order item count.
     * @param array|null $cachedOrder the cached order data. Defaults to `null`,
     * meaning the current cached order will be used.
     * @return int the item count of the cached order
     */
    public static function getCachedOrderItemCount($cachedOrder = null)
    {
        if ($cachedOrder === null) {
            $cachedOrder = self::getCachedOrder();
        }

        return count($cachedOrder['items']);
    }

    public function getOrderTotalPrice($clear = false)
    {
        $total = 0;
        foreach ($this->items as $item) {
            $price = $item->getPrice();
            $total += $price * $item->amount;
        }
        if ($clear)
            return $total;
        return number_format($total, 0, '.', ' ');
    }

    public function getOrderTotalRubPrice()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $price = $item->getRublePrice();
            $total += $price * $item->amount;
        }
        return $total;
    }

    public function getOrderTotalUsdPrice()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $price = $item->getUsdPrice();
            $total += $price * $item->amount;
        }
        return $total;
    }

    public function getOrderTotalSumPrice()
    {
        $total = 0;
        foreach ($this->items as $item) {
            $price = $item->getSumPrice();
            $total += $price * $item->amount;
        }
        return $total;
    }

    public static function getCurrency()
    {
        $cookies = Yii::$app->request->cookies;
        return $cookies->getValue('currency', self::CUR_SUM);
    }

    public static function getCurrencyText()
    {
        $currency = self::getCurrency();
        return mb_strtolower(Orders::$currency[$currency], "utf-8");
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "order_items".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $product_id
 * @property string $product_name
 * @property integer $amount
 * @property string $price
 *
 * @property Products $product
 * @property Orders $order
 */
class OrderItems extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order_items';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'product_id', 'price'], 'required'],
            [['order_id', 'product_id', 'amount'], 'integer'],
            [['price'], 'number'],
            [['product_name'], 'string', 'max' => 255],
            [['product_id'], 'exist', 'skipOnError' => true, 'targetClass' => Products::className(), 'targetAttribute' => ['product_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Orders::className(), 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'ID заказа',
            'product_id' => 'Продукт',
            'product_name' => 'Название',
            'amount' => 'Количество',
            'price' => 'Цена',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Products::className(), ['id' => 'product_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Orders::className(), ['id' => 'order_id']);
    }

    public function getSumPrice()
    {
        return $this->price;
    }

    public function getRublePrice()
    {
        return round($this->price / 120);
    }

    public function getUsdPrice()
    {
        return round($this->price / 9000);
    }

    public function getPrice()
    {
        $currency = Orders::getCurrency();
        $price = 0;
        if ($currency == Orders::CUR_SUM)
            $price = $this->getSumPrice();
        elseif ($currency == Orders::CUR_RUB)
            $price = $this->getRublePrice();
        elseif ($currency == Orders::CUR_USD)
            $price = $this->getUsdPrice();

        return $price;
    }
}

<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "paynet_payments".
 *
 * @property integer $id
 * @property integer $order_id
 * @property string $transaction_id
 * @property integer $service_id
 * @property double $amount
 * @property string $transaction_time
 * @property string $accept_time
 * @property integer $state
 * @property string $method
 * @property double $balance
 */
class PaynetPayments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'paynet_payments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'transaction_id', 'service_id','amount'], 'required'],
            [['order_id', 'service_id', 'state','balance'], 'integer'],
            [['amount'], 'number'],
            [['transaction_time', 'accept_time'], 'safe'],
            [['transaction_id'], 'string', 'max' => 100],
            [['method'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Client ID',
            'transaction_id' => 'Transaction ID',
            'service_id' => 'Service ID',
            'transaction_time' => 'Transaction Time',
            'state' => 'State',
            'method' => 'Method',
        ];
    }
}

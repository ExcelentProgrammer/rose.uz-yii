<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "click_payments".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $click_trans_id
 * @property integer $service_id
 * @property integer $click_paydoc_id
 * @property string $merchant_trans_id
 * @property double $amount
 * @property integer $action
 * @property integer $error
 * @property string $error_note
 * @property string $sign_time
 * @property string $sign_string
 * @property integer $state
 */
class ClickPayments extends \yii\db\ActiveRecord
{
    const STATE_CREATED = 0;
    const STATE_COMPLETE = 1;
    const STATE_CANCELED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'click_payments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'click_trans_id', 'service_id', 'click_paydoc_id', 'merchant_trans_id', 'amount', 'action', 'error', 'error_note', 'sign_time', 'sign_string'], 'required'],
            [['order_id', 'click_trans_id', 'service_id', 'click_paydoc_id', 'action', 'error','state'], 'integer'],
            [['amount'], 'number'],
            [['merchant_trans_id', 'error_note', 'sign_string'], 'string', 'max' => 255],
            [['sign_time'], 'string', 'max' => 25],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'User ID',
            'click_trans_id' => 'Click Trans ID',
            'service_id' => 'Service ID',
            'click_paydoc_id' => 'Click Paydoc ID',
            'merchant_trans_id' => 'Merchant Trans ID',
            'amount' => 'Amount',
            'action' => 'Action',
            'error' => 'Error',
            'error_note' => 'Error Note',
            'sign_time' => 'Sign Time',
            'sign_string' => 'Sign String',
        ];
    }
}

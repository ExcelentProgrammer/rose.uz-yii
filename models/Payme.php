<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "payme".
 *
 * @property integer $id
 * @property string $paycom_transaction_id
 * @property integer $paycom_time
 * @property string $paycom_time_datetime
 * @property string $create_time
 * @property string $perform_time
 * @property string $cancel_time
 * @property integer $state
 * @property integer $reason
 * @property integer $amount
 * @property string $receivers
 * @property string $order_id
 */
class Payme extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payme';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['paycom_transaction_id', 'paycom_time', 'paycom_time_datetime', 'create_time', 'state', 'amount', 'order_id'], 'required'],
            [['state', 'reason', 'amount', 'paycom_time'], 'integer'],
            [['paycom_transaction_id'], 'string', 'max' => 150],
            [['paycom_time_datetime'], 'string', 'max' => 30],
            [['create_time', 'perform_time', 'cancel_time', 'receivers', 'order_id'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'paycom_transaction_id' => 'Paycom Transaction ID',
            'paycom_time' => 'Paycom Time',
            'paycom_time_datetime' => 'Paycom Time Datetime',
            'create_time' => 'Create Time',
            'perform_time' => 'Perform Time',
            'cancel_time' => 'Cancel Time',
            'state' => 'State',
            'reason' => 'Reason',
            'amount' => 'Amount',
            'receivers' => 'Receivers',
            'order_id' => 'Order ID',
        ];
    }

    /**
     * Determines whether current transaction is expired or not.
     * @return bool true - if current instance of the transaction is expired, false - otherwise.
     */
    public function isExpired()
    {
        return $this->state == PaymeTransaction::STATE_CREATED && Format::datetime2timestamp($this->create_time) - time() > PaymeTransaction::TIMEOUT;
    }

    /**
     * Cancels transaction with the specified reason.
     * @param int $reason cancelling reason.
     * @return void
     */
    public function cancel($reason)
    {
        // todo: Implement transaction cancelling on data store
        // todo: Populate $cancel_time with value
        $this->cancel_time = Format::timestamp2datetime(Format::timestamp());
        // todo: Change $state to cancelled (-1 or -2) according to the current state
        // Scenario: CreateTransaction -> CancelTransaction
        $this->state = PaymeTransaction::STATE_CANCELLED;
        // Scenario: CreateTransaction -> PerformTransaction -> CancelTransaction
        if ($this->state == PaymeTransaction::STATE_COMPLETED) {
            $this->state = PaymeTransaction::STATE_CANCELLED_AFTER_COMPLETE;
        }
        // set reason
        $this->reason = $reason;
        $this->save();
        // todo: Update transaction on data store
    }

    public static function report($from, $to)
    {
        $from_date = Format::timestamp2datetime($from);
        $to_date = Format::timestamp2datetime($to);
        
        // todo: Retrieve transactions for the specified period from data store
        // assume, here we have $rows variable that is populated with transactions from data store
        // normalize data for response
        $result = [];
        $model = self::find()->where('create_time>=:f and create_time<=:t', [":f" => $from_date, ":t" => $to_date])->all();
        foreach ($model as $row) {
            $result[] = [
                'id' => $row->paycom_transaction_id, // paycom transaction id
                'time' => 1 * $row->paycom_time, // paycom transaction timestamp as is
                'amount' => 1 * $row->amount,
                'account' => [
                    'order_id' => $row->order_id, // account parameters to identify client/order/service
                    // ... additional parameters may be listed here, which are belongs to the account
                ],
                'create_time' => Format::datetime2timestamp($row->create_time),
                'perform_time' => Format::datetime2timestamp($row->perform_time),
                'cancel_time' => Format::datetime2timestamp($row->cancel_time),
                'transaction' => $row->id,
                'state' => 1 * $row->state,
                'reason' => isset($row->reason) ? 1 * $row->reason : null,
                'receivers' => $row->receivers
            ];
        }
        return $result;
    }

}

<?php

namespace app\models;

use Yii;
use yii\db\mssql\PDO;
use yii\helpers\VarDumper;

class PaymeTransaction
{
    /** Transaction expiration time in milliseconds. 43 200 000 ms = 12 hours. */
    const TIMEOUT = 43200000;

    const STATE_CREATED = 1;
    const STATE_COMPLETED = 2;
    const STATE_CANCELLED = -1;
    const STATE_CANCELLED_AFTER_COMPLETE = -2;

    const REASON_RECEIVERS_NOT_FOUND = 1;
    const REASON_PROCESSING_EXECUTION_FAILED = 2;
    const REASON_EXECUTION_FAILED = 3;
    const REASON_CANCELLED_BY_TIMEOUT = 4;
    const REASON_FUND_RETURNED = 5;
    const REASON_UNKNOWN = 10;

    const MERCHANT_ID = "5fa4e85980c1382f6432f637";
    const LOGIN = "Paycom";
    const KEY_FILE = "payme.password";

    public $key;
    public $keyPath;
    public $request_id;
    public $request;
    public $params;

    /** @var string Paycom transaction id. */
    public $paycom_transaction_id;
    /** @var int Paycom transaction time as is without change. */
    public $paycom_time;
    /** @var string Paycom transaction time as date and time string. */
    public $paycom_time_datetime;
    /** @var int Transaction id in the merchant's system. */
    public $id;
    /** @var string Transaction create date and time in the merchant's system. */
    public $create_time;
    /** @var string Transaction perform date and time in the merchant's system. */
    public $perform_time;
    /** @var string Transaction cancel date and time in the merchant's system. */
    public $cancel_time;
    /** @var int Transaction state. */
    public $state;
    /** @var int Transaction cancelling reason. */
    public $reason;
    /** @var int Amount value in coins, this is service or product price. */
    public $amount;
    /** @var string Pay receivers. Null - owner is the only receiver. */
    public $receivers;
    // additional fields:
    // - to identify order or product, for example, code of the order
    // - to identify client, for example, account id or phone number
    /** @var string Code to identify the order or service for pay. */
    public $order_id;

    public function __construct($request)
    {
        $this->request = json_decode($request, true);
        if (!$this->request) {
            throw new PaycomException(
                null,
                'Invalid JSON-RPC object.',
                PaycomException::ERROR_INVALID_JSON_RPC_OBJECT
            );
        }
        $this->request_id = $this->request['id'];
        $this->params = $this->request['params'];
        $this->keyPath = realpath(__DIR__ . '/../' . self::KEY_FILE);
        $this->key = file_get_contents($this->keyPath);

        try {
            $this->authorize();
            switch ($this->request['method']) {
                case 'CheckPerformTransaction':
                    $this->CheckPerformTransaction();
                    break;
                case 'CheckTransaction':
                    $this->CheckTransaction();
                    break;
                case 'CreateTransaction':
                    $this->CreateTransaction();
                    break;
                case 'PerformTransaction':
                    $this->PerformTransaction();
                    break;
                case 'CancelTransaction':
                    $this->CancelTransaction();
                    break;
                case 'ChangePassword':
                    $this->ChangePassword();
                    break;
                case 'GetStatement':
                    $this->GetStatement();
                    break;
                default:
                    $this->error(
                        PaycomException::ERROR_METHOD_NOT_FOUND,
                        'Method not found.',
                        $this->request->method
                    );
                    break;
            }
        } catch (PaycomException $exc) {
            $exc->send();
        }
    }

    private function authorize()
    {
        $headers = apache_request_headers();

        if (!$headers || !isset($headers['Authorization']) ||
            !preg_match('/^\s*Basic\s+(\S+)\s*$/i', $headers['Authorization'], $matches) ||
            base64_decode($matches[1]) != self::LOGIN . ":" . $this->key
        ) {
            $this->error(
                PaycomException::ERROR_INSUFFICIENT_PRIVILEGE,
                'Insufficient privilege to perform this method.'
            );
        }

        return true;
    }

    /**
     * Sends response with the given result and error.
     * @param mixed $result result of the request.
     * @param mixed|null $error error.
     */
    public function send($result, $error = null)
    {
        header('Content-Type: application/json; charset=UTF-8');
        $response['id'] = $this->request_id;
        $response['result'] = $result;
        $response['error'] = $error;
        echo json_encode($response);
    }

    /**
     * Generates PaycomException exception with given parameters.
     * @param int $code error code.
     * @param string|array $message error message.
     * @param string $data parameter name, that resulted to this error.
     * @throws PaycomException
     */
    private function error($code, $message = null, $data = null)
    {
        throw new PaycomException($this->request_id, $message, $code, $data);
    }

    private function CheckPerformTransaction()
    {
        $this->validate();

        $model = $this->findTransaction();
        if ($model !== null && ($model->state == self::STATE_CREATED || $model->state == self::STATE_COMPLETED)) {
            $this->error(
                PaycomException::ERROR_COULD_NOT_PERFORM,
                'There is other active/completed transaction for this order.'
            );
        } else
            $this->send(['allow' => true]);
    }

    private function CheckTransaction()
    {
        $model = $this->findTransaction();
        if ($model === null) {
            $this->error(
                PaycomException::ERROR_TRANSACTION_NOT_FOUND,
                'Transaction not found.'
            );
        }
        // todo: Prepare and send found transaction
        $this->send([
            'create_time' => Format::datetime2timestamp($model->create_time),
            'perform_time' => Format::datetime2timestamp($model->perform_time),
            'cancel_time' => Format::datetime2timestamp($model->cancel_time),
            'transaction' => $model->id,
            'state' => $model->state,
            'reason' => isset($model->reason) ? 1 * $model->reason : null
        ]);
    }

    private function CreateTransaction()
    {
        $this->validate();
        $model = $this->findTransaction();
        if ($model !== null) {
            if ($model->state != self::STATE_CREATED) { // validate transaction state
                $this->error(
                    PaycomException::ERROR_COULD_NOT_PERFORM,
                    'Transaction found, but is not active.'
                );
            } elseif ($model->isExpired()) { // if transaction timed out, cancel it and send error
                $model->cancel(self::REASON_CANCELLED_BY_TIMEOUT);
                $this->error(
                    PaycomException::ERROR_COULD_NOT_PERFORM,
                    'Transaction is expired.'
                );
            } else { // if transaction found and active, send it as response
                $this->send([
                    'create_time' => Format::datetime2milliseconds($model->create_time),
                    'transaction' => (string)$model->id,
                    'state' => $model->state,
                    'receivers' => $model->receivers
                ]);
            }
        } else {
            if (Format::timestamp(true) - Format::timestamp2milliseconds(1 * $this->params['time']) >= self::TIMEOUT) {
                $this->error(
                    PaycomException::ERROR_INVALID_ACCOUNT,
                    PaycomException::message(
                        'С даты создания транзакции прошло ' . self::TIMEOUT . 'мс',
                        'Tranzaksiya yaratilgan sanadan ' . self::TIMEOUT . 'ms o`tgan',
                        'Since create time of the transaction passed ' . self::TIMEOUT . 'ms'
                    ),
                    'time'
                );
            }

            $model = new Payme();
            $create_time = Format::timestamp(true);
            $model->paycom_transaction_id = $this->params['id'];
            $model->paycom_time = $this->params['time'];
            $model->paycom_time_datetime = Format::timestamp2datetime($this->params['time']);
            $model->create_time = Format::timestamp2datetime($create_time);
            $model->state = self::STATE_CREATED;
            $model->amount = $this->params['amount'];
            $model->order_id = $this->params['account']['order_id'];
            $model->save(); // after save $transaction->id will be populated with the newly created transaction's id.
            // send response
            $this->send([
                'create_time' => $create_time,
                'transaction' => (string)$model->id,
                'state' => $model->state,
                'receivers' => reset($model->firstErrors)
            ]);
        }
    }

    private function PerformTransaction()
    {
        $model = $this->findTransaction();
        if ($model === null) {
            $this->error(PaycomException::ERROR_TRANSACTION_NOT_FOUND, 'Transaction not found.');
        }
        switch ($model->state) {
            case self::STATE_CREATED: // handle active transaction
                if ($model->isExpired()) { // if transaction is expired, then cancel it and send error
                    $model->cancel(self::REASON_CANCELLED_BY_TIMEOUT);
                    $this->error(
                        PaycomException::ERROR_COULD_NOT_PERFORM,
                        'Transaction is expired.'
                    );
                } else { // perform active transaction
                    $perform_time = Format::timestamp();
                    $model->state = self::STATE_COMPLETED;
                    $model->perform_time = Format::timestamp2datetime($perform_time);

                    if ($model->save()) {
                        $payment = new Payments();
                        $payment->order_id = $model->order_id;
                        $payment->type = 1;
                        $payment->amount = $model->amount / 100;
                        $payment->service = "PAYME_PAYMENT";
                        $payment->save();

                        $order = Orders::findOne($model->order_id);
                        $order->state = Orders::STATE_PAID;
                        $order->payment_type = Orders::PAYMENT_PAYME;
                        $order->total_paid = $payment->amount;
                        $order->is_paid = 1;
                        $order->save();

                        $amountText = number_format($payment->amount, 0, '.', ' ');
                        Dashboard::sendNotification("Rose.uz: Оплата через Payme. ID заказа: {$model->order_id}. Сумма: {$amountText} сум");
                    }

                    $this->send([
                        'transaction' => (string)$model->id,
                        'perform_time' => (int)Format::datetime2milliseconds($model->perform_time),
                        'state' => $model->state
                    ]);
                }
                break;
            case self::STATE_COMPLETED: // handle complete transaction
                // todo: If transaction completed, just return it
                $this->send([
                    'transaction' => (string)$model->id,
                    'perform_time' => (int)Format::datetime2milliseconds($model->perform_time),
                    'state' => $model->state
                ]);
                break;
            default:
                // unknown situation
                $this->error(
                    PaycomException::ERROR_COULD_NOT_PERFORM,
                    'Could not perform this operation.'
                );
                break;
        }
    }

    private function CancelTransaction()
    {
        $model = $this->findTransaction();
        if ($model === null) {
            $this->error(PaycomException::ERROR_TRANSACTION_NOT_FOUND, 'Transaction not found.');
        }
        switch ($model->state) {
            // if already cancelled, just send it
            case self::STATE_CANCELLED:
                $this->send([
                    'transaction' => (string)$model->id,
                    'cancel_time' => Format::datetime2milliseconds($model->cancel_time),
                    'state' => self::STATE_CANCELLED
                ]);
                break;
            case self::STATE_CANCELLED_AFTER_COMPLETE:
                $this->send([
                    'transaction' => (string)$model->id,
                    'cancel_time' => Format::datetime2milliseconds($model->cancel_time),
                    'state' => self::STATE_CANCELLED_AFTER_COMPLETE
                ]);
                break;
            // cancel active transaction
            case self::STATE_CREATED:
            case self::STATE_COMPLETED:
                // cancel transaction with given reason
                $model->cancel(1 * $this->request->params['reason']);
                // after $found->cancel(), cancel_time and state properties populated with data
                // send response
                $this->send([
                    'transaction' => (string)$model->id,
                    'cancel_time' => Format::datetime2milliseconds($model->cancel_time),
                    'state' => $model->state
                ]);
                break;
        }
    }

    private function ChangePassword()
    {
        // validate, password is specified, otherwise send error
        if (!isset($this->params['password']) || !trim($this->params['password'])) {
            $this->error(PaycomException::ERROR_INVALID_ACCOUNT, 'New password not specified.', 'password');
        }
        // if current password specified as new, then send error
        if ($this->key == $this->params['password']) {
            $this->error(PaycomException::ERROR_INSUFFICIENT_PRIVILEGE, 'Insufficient privilege. Incorrect new password.');
        }
        // todo: Implement saving password into data store or file
        // example implementation, that saves new password into file specified in the configuration
        if (!file_put_contents($this->keyPath, $this->params['password'])) {
            $this->error(PaycomException::ERROR_INTERNAL_SYSTEM, 'Internal System Error.');
        }
        // if control is here, then password is saved into data store
        // send success response
        $this->send(['success' => true]);
    }

    private function GetStatement()
    {
        // validate 'from'
        if (!isset($this->params['from'])) {
            $this->error(PaycomException::ERROR_INVALID_ACCOUNT, 'Incorrect period.', 'from');
        }
        // validate 'to'
        if (!isset($this->params['to'])) {
            $this->error(PaycomException::ERROR_INVALID_ACCOUNT, 'Incorrect period.', 'to');
        }
        // validate period
        if (1 * $this->params['from'] >= 1 * $this->params['to']) {
            $this->error(PaycomException::ERROR_INVALID_ACCOUNT, 'Incorrect period. (from >= to)', 'from');
        }
        // get list of transactions for specified period
        $transactions = Payme::report($this->params['from'], $this->params['to']);
        // send results back
        $this->send(['transactions' => $transactions]);
    }

    /**
     * Validates amount and account values.
     * @param array $params amount and account parameters to validate.
     * @return bool true - if validation passes
     * @throws PaycomException - if validation fails
     */
    private function validate()
    {
        // todo: Validate amount, if failed throw error
        // for example, check amount is numeric

        if (!preg_match("/^[0-9]*$/",$this->params['amount'])) {

            throw new PaycomException(
                $this->request_id,
                'Incorrect amount.',
                PaycomException::ERROR_INVALID_AMOUNT
            );
        }
        // todo: Validate account, if failed throw error
        // assume, we should have order_id
        $order = $this->findOrder();
        if (!isset($this->params['account']['order_id']) || $order === null) {
            throw new PaycomException(
                $this->request_id,
                PaycomException::message(
                    'Неверный код заказа.',
                    'Harid kodida xatolik.',
                    'Incorrect order code.'
                ),
                PaycomException::ERROR_INVALID_ACCOUNT,
                'order_id'
            );
        }

        if ($order->getOrderTotalPrice(true) * 100 != 1 * $this->params['amount']) {
            throw new PaycomException(
                $this->request_id,
                'Incorrect amount.',
                PaycomException::ERROR_INVALID_AMOUNT,
                'amount'
            );
        }
        return true;
    }

    /**
     * @return Payme the loaded model
     */
    private function findTransaction()
    {
        return Payme::find()->where(['paycom_transaction_id' => $this->params['id']])->one();
    }

    /**
     * @return Orders the loaded model
     */
    private function findOrder()
    {
        return Orders::find()->where(['id' => $this->params['account']['order_id'], 'state' => Orders::STATE_CREATED, 'is_paid' => 0])->one();
    }
}

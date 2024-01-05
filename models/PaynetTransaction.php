<?php

namespace app\models;

class PaynetTransaction
{
    const LOGIN = "tulip";
    const KEY_FILE = "paynet.password";

    public $amount;
    public $orderId;
    public $serviceId;
    public $transactionId;
    public $transactionTime;
    public $dateTo;
    public $dateFrom;
    public $method;
    public $response;

    public function __construct($request)
    {
        $keyPath = realpath(__DIR__ . '/../' . self::KEY_FILE);
        $password = file_get_contents($keyPath);

        $data = json_decode(json_encode(simplexml_load_string(preg_replace('/(<\/?)(\w+):([^>]*>)/', '$1$2$3', $request))), true);
//        return $data;
        foreach ($data['soapenvBody'] as $method => $value) {
            if ($value['username'] != self::LOGIN || $value['password'] != $password) {
                return false;
            }
            $this->amount = ($value['amount'] / 100);
            $this->serviceId = $value['serviceId'];
            $this->newPassword = $value['newPassword'];
            $this->transactionId = $value['transactionId'];
            $this->transactionTime = date_create($value['transactionTime']);
            if ($value['parameters']['paramKey'] == 'order_id')
                $this->orderId = $value['parameters']['paramValue'];
            if ($value['parameters']['paramKey'] == 'getInfoType')
                $this->getInfo = $value['parameters']['paramValue'];
            $this->dateFrom = date_create($value['dateFrom']);
            $this->dateTo = date_create($value['dateTo']);
            $this->method = str_replace('ns1', '', $method);
        }
        switch ($this->method) {
            case 'PerformTransactionArguments': {
                $this->PerformTransaction();
                break;
            }
            case 'CheckTransactionArguments': {
                $this->CheckTransaction();
                break;
            }
            case 'CancelTransactionArguments': {
                $this->CancelTransaction();
                break;
            }
            case 'GetStatementArguments': {
                $this->GetStatement();
                break;
            }
            case 'GetInformationArguments': {
                $this->GetInformation();
                break;
            }
            case 'ChangePasswordArguments': {
                $this->ChangePasswordArguments();
                break;
            }
        }
        return true;
    }

    private function PerformTransaction()
    {
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $order = Orders::find()->where(['id' => $this->orderId, 'state' => Orders::STATE_CREATED])->one();
        if ($order === null) {
            $this->response = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
<soapenv:Body>
<ns2:PerformTransactionResult xmlns:ns2="http://uws.provider.com/">
<errorMsg>Order is not found</errorMsg>
<status>302</status>
<timeStamp>{$date}T{$time}+05:00</timeStamp>
<providerTrnId>{$this->transactionId}</providerTrnId>
</ns2:PerformTransactionResult>
</soapenv:Body>
</soapenv:Envelope> 
XML;
            return false;
        }
        $transaction = PaynetPayments::find()->where(['service_id' => $this->serviceId, 'transaction_id' => $this->transactionId])->one();
        if ($transaction !== null) {
            $this->response = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
<soapenv:Body>
<ns2:PerformTransactionResult xmlns:ns2="http://uws.provider.com/">
<errorMsg>Transaction is already exist</errorMsg>
<status>201</status>
<timeStamp>{$date}T{$time}+05:00</timeStamp>
<providerTrnId>{$transaction->id}</providerTrnId>
</ns2:PerformTransactionResult>
</soapenv:Body>
</soapenv:Envelope> 
XML;
            return false;
        }
        $model = new PaynetPayments();
        $model->order_id = $this->orderId;
        $model->transaction_id = $this->transactionId;
        $model->service_id = $this->serviceId;
        $model->amount = $this->amount;
        $model->transaction_time = date_format($this->transactionTime, "Y-m-d H:i:s");
        $model->accept_time = date("Y-m-d H:i:s");
        $model->state = 1;
        $model->method = $this->method;
        if ($model->save()) {
            $payment = new Payments();
            $payment->order_id = $model->order_id;
            $payment->type = 1;
            $payment->amount = $model->amount;
            $payment->service = "PAYNET_PAYMENT";
            $payment->save();

            $order = Orders::findOne($model->order_id);
            $order->state = Orders::STATE_PAID;
            $order->payment_type = Orders::PAYMENT_PAYNET;
            $order->total_paid = $payment->amount;
            $order->save();

//            $amountText = number_format($payment->amount, 0, '.', ' ');
//            TelegramBot::sendNotification("Tulip.uz: Оплата через Paynet. ID заказа: {$model->order_id}. Сумма: {$amountText} сум");

            $this->response = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
<soapenv:Body>
<ns2:PerformTransactionResult xmlns:ns2="http://uws.provider.com/">
<errorMsg>Ok</errorMsg>
<status>0</status>
<timeStamp>{$date}T{$time}+05:00</timeStamp>
<providerTrnId>{$model->id}</providerTrnId>
</ns2:PerformTransactionResult>
</soapenv:Body>
</soapenv:Envelope> 
XML;
        } else {
            print_r($model->firstErrors);
        }
        return true;
    }

    private function CheckTransaction()
    {
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $model = PaynetPayments::find()->where(['service_id' => $this->serviceId, 'transaction_id' => $this->transactionId])->one();
        if ($model === null) {
            $this->response = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
<soapenv:Body>
<ns2:CheckTransactionResult xmlns:ns2="http://uws.provider.com/">
<errorMsg>Transaction not found</errorMsg>
<status>303</status>
<timeStamp>{$date}T{$time}+05:00</timeStamp>
<providerTrnId>{$model->id}</providerTrnId>
<transactionState>2</transactionState>
<transactionStateErrorStatus>303</transactionStateErrorStatus>
<transactionStateErrorMsg>Transaction not found</transactionStateErrorMsg>
</ns2:CheckTransactionResult>
</soapenv:Body>
</soapenv:Envelope>
XML;
            return false;
        }
        $this->response = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
<soapenv:Body>
<ns2:CheckTransactionResult xmlns:ns2="http://uws.provider.com/">
<errorMsg>Ok</errorMsg>
<status>0</status>
<timeStamp>{$date}T{$time}+05:00</timeStamp>
<providerTrnId>{$model->id}</providerTrnId>
<transactionState>{$model->state}</transactionState>
<transactionStateErrorStatus>0</transactionStateErrorStatus>
<transactionStateErrorMsg>Success</transactionStateErrorMsg>
</ns2:CheckTransactionResult>
</soapenv:Body>
</soapenv:Envelope> 
XML;
        return true;
    }

    private function CancelTransaction()
    {
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $model = PaynetPayments::find()->where(['service_id' => $this->serviceId, 'transaction_id' => $this->transactionId])->one();
        if ($model === null) {
            $this->response = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
    <soapenv:Body>
        <ns2:CancelTransactionResult xmlns:ns2="http://uws.provider.com/">
            <errorMsg>Transaction not found or canceled</errorMsg>
            <status>303</status>
            <timeStamp>{$date}T{$time}+05:00</timeStamp>
            <transactionState>2</transactionState>
        </ns2:CancelTransactionResult>
    </soapenv:Body>
</soapenv:Envelope>
XML;
            return false;
        }
        if ($model->state == 2) {
            $this->response = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
    <soapenv:Body>
        <ns2:CancelTransactionResult xmlns:ns2="http://uws.provider.com/">
            <errorMsg>Транзакция уже отменена</errorMsg>
            <status>202</status>
            <timeStamp>{$date}T{$time}+05:00</timeStamp>
            <transactionState>2</transactionState>
        </ns2:CancelTransactionResult>
    </soapenv:Body>
</soapenv:Envelope>
XML;
            return false;
        }

        $model->state = 2;
        if ($model->save()) {
//            Payments::find()->where(['service'=>'PAYNET_PAYMENT','chat_id'=>$model->client_id]);
            $this->response = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
    <soapenv:Body>
        <ns2:CancelTransactionResult xmlns:ns2="http://uws.provider.com/">
            <errorMsg>Ok</errorMsg>
            <status>0</status>
            <timeStamp>{$date}T{$time}+05:00</timeStamp>
            <transactionState>{$model->state}</transactionState>
        </ns2:CancelTransactionResult>
    </soapenv:Body>
</soapenv:Envelope>
XML;
        }
        return true;
    }

    private function GetStatement()
    {
        $stm = null;
        $models = PaynetPayments::find()->where('transaction_time>=:sd and transaction_time<=:ed and service_id=:s and state=1', [':sd' => date_format($this->dateFrom, "Y-m-d H:i:s"), ':ed' => date_format($this->dateTo, "Y-m-d H:i:s"), ':s' => $this->serviceId])->all();
        foreach ($models as $model) {
            $date = date_create($model->transaction_time);
            $d = date_format($date, "Y-m-d");
            $t = date_format($date, "H:i:s");
            $amount = $model->amount * 100;
            $stm .= <<<STM
                <statements>
                    <amount>{$amount}</amount>
                    <providerTrnId>{$model->id}</providerTrnId>
                    <transactionId>$model->transaction_id</transactionId> <transactionTime>{$d}T{$t}+05:00</transactionTime>
                </statements>
STM;
        }
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $this->response = <<<XML
<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
    <s:Body xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema">
        <uws:GetStatementResult xmlns:uws="http://uws.provider.com/">
            <errorMsg>Ok</errorMsg>
            <status>0</status>
            <timeStamp>{$date}T{$time}+05:00</timeStamp>
            {$stm}
        </uws:GetStatementResult>
    </s:Body>
</s:Envelope>
XML;
        return true;
    }

    private function GetInformation()
    {
        $date = date("Y-m-d");
        $time = date("H:i:s");
        $model = Orders::findOne($this->orderId);
        if ($model === null) {
            $this->response = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
    <soapenv:Body>
        <ns2:GetInformationResult xmlns:ns2="http://uws.provider.com/">
            <errorMsg>Order not found</errorMsg>
            <status>302</status>
            <timeStamp>{$date}T{$time}+05:00</timeStamp>
        </ns2:GetInformationResult>
    </soapenv:Body>
</soapenv:Envelope>
XML;
            return true;
        }
        $this->response = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/">
    <soapenv:Body><ns2:GetInformationResult xmlns:ns2="http://uws.provider.com/">
        <errorMsg>Success</errorMsg>
        <status>0</status>
        <timeStamp>{$date}T{$time}+05:00</timeStamp>
        <parameters>
            <paramKey>order</paramKey>
            <paramValue>{$model->id}</paramValue>
         </parameters>
    </ns2:GetInformationResult>
    </soapenv:Body>
</soapenv:Envelope>
XML;
        return true;
    }

    private function ChangePasswordArguments()
    {
        $date = date("Y-m-d");
        $time = date("H:i:s");
        if (!file_put_contents(realpath(__DIR__ . '/../' . self::KEY_FILE), $this->newPassword)) {
            $this->response = <<<XML
<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
    <s:Body xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema">
        <uws:ChangePasswordResult xmlns:uws="http://uws.provider.com/">
            <errorMsg>Can not change password</errorMsg>
            <status>1</status>
            <timeStamp>{$date}T{$time}+05:00</timeStamp>
        </uws:ChangePasswordResult>
    </s:Body>
</s:Envelope>
XML;
            return true;
        }
        $this->response = <<<XML
<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
    <s:Body xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema">
        <uws:ChangePasswordResult xmlns:uws="http://uws.provider.com/">
            <errorMsg>Ok</errorMsg>
            <status>0</status>
            <timeStamp>{$date}T{$time}+05:00</timeStamp>
        </uws:ChangePasswordResult>
    </s:Body>
</s:Envelope>
XML;
        return true;
    }
}
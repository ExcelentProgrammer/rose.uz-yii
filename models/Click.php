<?php
namespace app\models;


class Click
{
    const SECRET_KEY = "nS@uwhxCI3Cf2htC7LlKC";
    const MERCHANT_ID = 6777;
    const MERCHANT_USER_ID = 7715;
    const SERVICE_ID = 8773;

    public $date = '';
    public $transAmount = '';
    public $signString = '';
    public $transId;

    public $prepare = [];
    public static $messages = array(
        0 => array("error" => "0", "error_note" => "success"),
        1 => array("error" => "-1", "error_note" => "SIGN CHECK FAILED!"),
        2 => array("error" => "-2", "error_note" => "Incorrect parameter amount"),
        3 => array("error" => "-3", "error_note" => "Action not found"),
        4 => array("error" => "-4", "error_note" => "Already paid"),
        5 => array("error" => "-5", "error_note" => "User does not exist"),
        6 => array("error" => "-6", "error_note" => "Transaction does not exist"),
        7 => array("error" => "-7", "error_note" => "Failed to update user"),
        8 => array("error" => "-8", "error_note" => "Error in request from click"),
        9 => array("error" => "-9", "error_note" => "Transaction cancelled"),
        'n' => array("error" => "-n", "error_note" => "Unknown Error")
    );

    public function __construct($id)
    {
        $model = Orders::findOne($id);
        $this->transId = $model->id;
        date_default_timezone_set('Asia/Tashkent');
        $this->date = date("Y-m-d h:i:s");
        $totalPrice = $model->getOrderTotalSumPrice();
        $this->transAmount = number_format($totalPrice, 2, '.', '');
        $this->signString = md5($this->date . self::SECRET_KEY . self::SERVICE_ID . $this->transId . $this->transAmount);
    }

}
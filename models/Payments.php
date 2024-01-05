<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "payments".
 *
 * @property integer $id
 * @property integer $order_id
 * @property integer $type
 * @property double $amount
 * @property string $service
 * @property string $date
 * @property string $description
 * @property double $balance
 *
 */
class Payments extends \yii\db\ActiveRecord
{
    public static $months = [
        1 => 'январь',
        2 => 'февраль',
        3 => 'март',
        4 => 'апрель',
        5 => 'май',
        6 => 'июнь',
        7 => 'июль',
        8 => 'август',
        9 => 'сентябрь',
        10 => 'октябрь',
        11 => 'ноябрь',
        12 => 'декабрь',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'amount', 'service'], 'required'],
            [['order_id', 'type'], 'integer'],
            [['amount', 'balance'], 'number'],
            [['date'], 'safe'],
            [['service'], 'string', 'max' => 25],
            [['description'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Заказ',
            'type' => 'Тип',
            'amount' => 'Сумма',
            'service' => 'Сервис',
            'date' => 'Дата',
            'description' => 'Описание',
            'balance' => 'Остаток',
        ];
    }

    public static function getYears()
    {
        $r = [];
        for ($i = date("Y"); $i >= 2017; $i--) {
            $r[$i] = $i;
        }
        return $r;
    }

    public static function getServiceFilter()
    {
        $db = Yii::$app->db;
        $cmd = $db->createCommand("select service from payments group by service");
        $data = $cmd->queryColumn();
        $r = [];
        foreach ($data as $service) {
            $r[$service] = $service;
        }
        return $r;
    }
}

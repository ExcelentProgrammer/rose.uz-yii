<?php

namespace app\models;

use Imagick;
use Yii;
use yii\base\Model;
use yii\db\mssql\PDO;

class Dashboard extends Model
{
    public static $months = [
        1 => 'Январь',
        2 => 'Февраль',
        3 => 'Март',
        4 => 'Апрель',
        5 => 'Май',
        6 => 'Июнь',
        7 => 'Июль',
        8 => 'Август',
        9 => 'Сентябрь',
        10 => 'Октябрь',
        11 => 'Ноябрь',
        12 => 'Декабрь',
    ];

    public static $languages = ["ru"];

    public static function getYears()
    {
        $y = [];
        for ($i = 2017; $i <= date("Y"); $i++) {
            $y[$i] = $i;
        }
        return $y;
    }

    public static function isNavActive($controller, $action = '')
    {
        $yiiController = \Yii::$app->controller->id;
        $yiiAction = \Yii::$app->controller->action->id;
        if ($yiiController == $controller) {
            if (!empty($action) && $yiiAction != $action)
                return false;
            return true;
        }
        return false;
    }

    public static function sendNotification($text)
    {
        $bot = new TelegramBot();
        $bot->sendMessage(1769851684, $text);
        return $bot->sendMessage(101361497, $text);
    }

    public static function cropImage($file, $savePath, $width, $height)
    {
        $thumb = new Imagick();
        $thumb->readImage($file);
        $thumb->cropThumbnailImage($width, $height);
        $thumb->setImageCompression(Imagick::COMPRESSION_LZW);
        $thumb->setImageCompressionQuality(90);
        $thumb->writeImage($savePath);
        $thumb->clear();
        $thumb->destroy();
        return true;
    }

    public static function getTodayStat()
    {
        $date = date("Y-m-d");

        //Not confirmed
        $sql = "select count(*) from orders o where date(o.date)=:d and o.state=0";
        $n = Yii::$app->db->createCommand($sql)
            ->bindValue(":d", $date, PDO::PARAM_STR)
            ->queryScalar();

        //Canceled
        $sql = "select count(*) from orders o where date(o.date)=:d and o.state=6";
        $c = Yii::$app->db->createCommand($sql)
            ->bindValue(":d", $date, PDO::PARAM_STR)
            ->queryScalar();

        //Accepted
        $sql = "select count(*) from orders o where date(o.date)=:d and o.state>1 and o.state<6";
        $a = Yii::$app->db->createCommand($sql)
            ->bindValue(":d", $date, PDO::PARAM_STR)
            ->queryScalar();

        return [$n, $c, $a];
    }

    public static function getMonthStat($month, $year)
    {
        $r = [];
        $db = Yii::$app->db;
        $days = date("t", strtotime("{$year}-{$month}-01"));
        for ($i = 1; $i <= $days; $i++) {
            $r[0][$i] = 0;
            $r[1][$i] = 0;
            $r[2][$i] = 0;
        }

        //Not confirmed
        $sql = "select day(o.date) as day,ifnull(count(*),0) as total from orders o 
                where month(o.date)=:m and year(o.date)=:y and o.state=0
                group by day(o.date)";
        $n = $db->createCommand($sql)
            ->bindValue(":m", $month, PDO::PARAM_STR)
            ->bindValue(":y", $year, PDO::PARAM_STR)
            ->queryAll();
        foreach ($n as $row) {
            $r[0][$row['day']] = $row['total'];
        }

        //Canceled
        $sql = "select day(o.date) as day,ifnull(count(*),0) as total from orders o 
                where month(o.date)=:m and year(o.date)=:y and o.state=6
                group by day(o.date)";
        $c = $db->createCommand($sql)
            ->bindValue(":m", $month, PDO::PARAM_STR)
            ->bindValue(":y", $year, PDO::PARAM_STR)
            ->queryAll();
        foreach ($c as $row) {
            $r[1][$row['day']] = $row['total'];
        }

        //Accepted
        $sql = "select day(o.date) as day,ifnull(count(*),0) as total from orders o 
                where month(o.date)=:m and year(o.date)=:y and o.state>1 and o.state<6
                group by day(o.date)";
        $a = $db->createCommand($sql)
            ->bindValue(":m", $month, PDO::PARAM_STR)
            ->bindValue(":y", $year, PDO::PARAM_STR)
            ->queryAll();
        foreach ($a as $row) {
            $r[2][$row['day']] = $row['total'];
        }

        return $r;
    }

    public static function getYearStat($year)
    {
        $r = [];
        $db = Yii::$app->db;

        for ($i = 1; $i <= 12; $i++) {
            $r[0][$i] = 0;
            $r[1][$i] = 0;
            $r[2][$i] = 0;
        }

        //Not confirmed
        $sql = "select month(o.date) as month,ifnull(count(*),0) as total from orders o 
                where year(o.date)=:y and o.state=0
                group by month(o.date)";
        $n = $db->createCommand($sql)->bindValue(":y", $year, PDO::PARAM_STR)->queryAll();
        foreach ($n as $row) {
            $r[0][$row['month']] = $row['total'];
        }

        //Canceled
        $sql = "select month(o.date) as month,ifnull(count(*),0) as total from orders o 
                where year(o.date)=:y and o.state=6
                group by month(o.date)";
        $c = $db->createCommand($sql)->bindValue(":y", $year, PDO::PARAM_STR)->queryAll();
        foreach ($c as $row) {
            $r[1][$row['month']] = $row['total'];
        }

        //Accepted
        $sql = "select month(o.date) as month,ifnull(count(*),0) as total from orders o 
                where year(o.date)=:y and o.state>1 and o.state<6
                group by month(o.date)";
        $a = $db->createCommand($sql)->bindValue(":y", $year, PDO::PARAM_STR)->queryAll();
        foreach ($a as $row) {
            $r[2][$row['month']] = $row['total'];
        }

        return $r;
    }

    public static function price($price)
    {
        return number_format($price, 0, "", " ");
    }
}
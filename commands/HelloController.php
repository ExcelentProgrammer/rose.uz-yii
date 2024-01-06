<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\TelegramBot;
use Exception;
use SimpleXMLElement;
use Yii;
use yii\console\Controller;
use yii\db\mssql\PDO;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class HelloController extends Controller
{
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     */
    public function actionIndex($message = 'hello world')
    {
        echo $message . "\n";
    }

    public function actionParse()
    {
        $date = date("Y-m-d");
        $url = "http://cbu.uz/uzc/arkhiv-kursov-valyut/xml/USD/{$date}/";

        $xml = file_get_contents($url);
        if (empty($xml))
            return;
        try {
            $xml = new SimpleXMLElement($xml);
            $db = Yii::$app->db;
            foreach ($xml->CcyNtry as $object) {
                if ($object->Ccy == 'USD') {
                    $db->createCommand("insert into courses(date,currency,rate) values(:d,:c,:r) on duplicate key update rate=values(rate)")
                        ->bindValue(":d", date_format(date_create($object->date), "Y-m-d"), PDO::PARAM_STR)
                        ->bindValue(":c", $object->Ccy, PDO::PARAM_STR)
                        ->bindValue(":r", $object->Rate, PDO::PARAM_STR)
                        ->execute();
                }
            }
            echo "Course parsed\n";
        } catch (Exception $e) {
            print_r($e->getMessage());
        }

    }

}

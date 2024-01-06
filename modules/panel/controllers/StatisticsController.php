<?php

namespace app\modules\panel\controllers;

use app\components\AccessRule;
use app\models\User;
use Yii;
use yii\data\ArrayDataProvider;
use yii\db\mssql\PDO;
use yii\filters\AccessControl;
use yii\web\Controller;

/**
 * ChatsController implements the CRUD actions for Chats model.
 */
class StatisticsController extends Controller
{
    public $layout = 'panel';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'only' => ['*'],
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => [
                            User::ROLE_ADMIN
                        ],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex($start = null, $end = null)
    {
        if ($start == null)
            $start = date("Y-m-01");
        if ($end == null) {
            $end = date("Y-m-t");
        }
        $send = date("Y-m-d", strtotime($end . " +1 day"));

        $sql = "select date(o.date) as date,count(*) as count,sum(o.total) as total from orders o where o.date between :s and :e and o.state>1 and o.state<6 group by date(o.date)";

        $data = Yii::$app->db->createCommand($sql)
            ->bindValue(":s", $start, PDO::PARAM_STR)
            ->bindValue(":e", $send, PDO::PARAM_STR)
            ->queryAll();

        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'sort' => [
                'attributes' => ['date', 'total', 'count'],
            ],
            'pagination' => false,
        ]);

        return $this->render('index', [
            'start' => $start,
            'end' => $end,
            'dataProvider' => $dataProvider,
        ]);
    }

}

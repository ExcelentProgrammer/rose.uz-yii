<?php

namespace app\modules\panel\controllers;

use app\components\AccessRule;
use app\models\User;
use Yii;
use app\models\Payments;
use app\models\PaymentSearch;
use yii\db\mssql\PDO;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PaymentsController implements the CRUD actions for Payments model.
 */
class PaymentsController extends Controller
{
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
                        'actions' => ['index', 'stats', 'invoice', 'view', 'total'],
                        'allow' => true,
                        'roles' => [
                            User::ROLE_ADMIN
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Payments models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PaymentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionTotal()
    {
        $start = Yii::$app->request->get('start') ? Yii::$app->request->get('start') : date("Y-m-01");
        $end = Yii::$app->request->get('end') ? Yii::$app->request->get('end') : date('Y-m-t');

        $sql = "select SUM(IF(p.service='CLICK_PAYMENT',p.amount,0)) as click,
                       SUM(IF(p.service='PAYME_PAYMENT',p.amount,0))as payme,
                       SUM(IF(p.service='PAYNET_PAYMENT',p.amount,0)) as paynet
                  from payments p 
                  where p.type=1
                  and p.date>=:s and p.date<:e + INTERVAL 1 day";
        $cmd = Yii::$app->db->createCommand($sql)
            ->bindValue(":s", $start, PDO::PARAM_STR)
            ->bindValue(":e", $end, PDO::PARAM_STR);
        $data = $cmd->queryOne();
        return $this->render('total', [
            'data' => $data,
            'start' => $start,
            'end' => $end
        ]);
    }

    public function actionStats()
    {
        $db = Yii::$app->db;
        $radio_id = Yii::$app->request->get('radio_id');
        $start = Yii::$app->request->get('start');
        $end = Yii::$app->request->get('end');
        $short_number = Yii::$app->request->get('short_number');
        $keyword = Yii::$app->request->get('keyword');

        if (empty($start))
            $start = date("Y-m-01");

        if (empty($end))
            $end = date("Y-m-t");

        if (empty($radio_id))
            $radio_id = null;

        if (empty($short_number))
            $short_number = null;

        if (empty($keyword))
            $keyword = null;

        $cmd = $db->createCommand("CALL Get_radio_detail_statistics(:s,:e,:r,:sh,:k,@result_code,@result_text,@where_filtered);")
            ->bindValue(':s', $start, PDO::PARAM_STR)
            ->bindValue(':e', $end, PDO::PARAM_STR)
            ->bindValue(':r', $radio_id, PDO::PARAM_INT)
            ->bindValue(':sh', $short_number, PDO::PARAM_INT)
            ->bindValue(':k', $keyword, PDO::PARAM_STR);
//        return print_r($cmd->rawSql);
        $data = $cmd->queryAll();

        return $this->render('stats', [
            'data' => $data,
            'start' => $start,
            'end' => $end,
            'radio_id' => $radio_id,
            'short_number' => $short_number,
            'keyword' => $keyword,
        ]);
    }

    public function actionInvoice()
    {
        $data = [];
        $user = Yii::$app->request->get('user');
        $month = Yii::$app->request->get('month');
        $year = Yii::$app->request->get('year');
        if (!empty($user) && !empty($month) && !empty($year)) {
            $db = Yii::$app->db;
            if ($month < 10)
                $month = '0' . $month;
            $date = date_create($year . '-' . $month . '-01');
            $start = date_format($date, "Y-m-01");
            $end = date_format($date, "Y-m-t");
            $cmd = $db->createCommand("CALL Get_radio_statistics(:u,:s,:e,@result_code,@result_text,@where_filtered);")
                ->bindValue(":u", $user, PDO::PARAM_INT)
                ->bindValue(":s", $start, PDO::PARAM_STR)
                ->bindValue(":e", $end, PDO::PARAM_STR);
//            return print_r($cmd->rawSql);
            $data = $cmd->queryAll();
        }

        return $this->render('invoice', [
            'data' => $data,
            'user' => $user,
            'month' => (int)$month,
            'year' => $year,
        ]);
    }

    /**
     * Displays a single Payments model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Finds the Payments model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Payments the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Payments::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

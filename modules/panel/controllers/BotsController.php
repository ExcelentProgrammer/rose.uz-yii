<?php

namespace app\modules\panel\controllers;

use app\components\AccessRule;
use app\models\User;
use Yii;
use app\models\Bots;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * BotsController implements the CRUD actions for Bots model.
 */
class BotsController extends Controller
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
                        'actions' => ['index', 'create', 'delete', 'update', 'view'],
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
     * Lists all Bots models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Bots::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Bots model.
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
     * Creates a new Bots model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Bots();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Запись сохранена!');
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка!');
            }
            return $this->redirect(['index']);
        }
        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing Bots model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->isAjax) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ActiveForm::validate($model);
            }
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Запись сохранена!');
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка!');
            }
            return $this->redirect(['index']);
        } else {
            if (Yii::$app->request->isAjax) {
                return $this->renderAjax('update', ['model' => $model]);
            } else {
                return $this->render('update', ['model' => $model]);
            }
        }
    }

    /**
     * Deletes an existing Bots model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->delete())
            Yii::$app->session->setFlash('success', 'Запись сохранена!');
        else
            Yii::$app->session->setFlash('error', 'Ошибка!');

        return $this->redirect(['index']);
    }

    /**
     * Finds the Bots model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Bots the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Bots::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

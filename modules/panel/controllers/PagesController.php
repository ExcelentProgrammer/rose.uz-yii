<?php

namespace app\modules\panel\controllers;

use app\components\AccessRule;
use app\models\Dashboard;
use app\models\User;
use Yii;
use app\models\Pages;
use app\models\PageSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * PagesController implements the CRUD actions for Pages model.
 */
class PagesController extends Controller
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
            'actions' => ['index', 'create', 'update', 'view'],
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
   * Lists all Pages models.
   * @return mixed
   */
  public function actionIndex()
  {
    $searchModel = new PageSearch();
    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    return $this->render('index', [
      'searchModel' => $searchModel,
      'dataProvider' => $dataProvider,
    ]);
  }

  /**
   * Displays a single Pages model.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionView($id)
  {
    return $this->render('view', [
      'model' => $this->findModel($id),
    ]);
  }

  /**
   * Creates a new Pages model.
   * If creation is successful, the browser will be redirected to the 'view' page.
   * @return mixed
   */
  public function actionCreate()
  {
    $model = new Pages();

    if ($model->load(Yii::$app->request->post())) {
      $model->date = date("Y-m-d H:i:s");
      $model->author = Yii::$app->user->identity->fullname;
      $model->file = UploadedFile::getInstance($model, 'file');
      if ($model->file) {
        $fileName = 'page_' . time() . '.' . $model->file->extension;
        $savePath = Yii::getAlias('@uploads') . '/pages/' . $fileName;
        if ($model->file->saveAs($savePath)) {
          $model->image = $fileName;
//          Dashboard::scaleImage($savePath, $savePath, 1140, 500);
        }
      }

      if ($model->save())
        return $this->redirect(['view', 'id' => $model->id]);
    }

    return $this->render('create', [
      'model' => $model,
    ]);
  }

  /**
   * Updates an existing Pages model.
   * If update is successful, the browser will be redirected to the 'view' page.
   * @param integer $id
   * @return mixed
   * @throws NotFoundHttpException if the model cannot be found
   */
  public function actionUpdate($id)
  {
    $model = $this->findModel($id);

    if ($model->load(Yii::$app->request->post())) {
      $model->date = date("Y-m-d H:i:s");
      $model->author = Yii::$app->user->identity->fullname;
      $model->file = UploadedFile::getInstance($model, 'file');
      if ($model->file) {
        $fileName = 'page_' . time() . '.' . $model->file->extension;
        $savePath = Yii::getAlias('@uploads') . '/pages/' . $fileName;
        if ($model->file->saveAs($savePath)) {
          $model->image = $fileName;
//          Dashboard::scaleImage($savePath, $savePath, 1140, 500);
        }
      }
      if ($model->save())
        return $this->redirect(['view', 'id' => $model->id]);
    }

    return $this->render('update', [
      'model' => $model,
    ]);
  }

  /**
   * Finds the Pages model based on its primary key value.
   * If the model is not found, a 404 HTTP exception will be thrown.
   * @param integer $id
   * @return Pages the loaded model
   * @throws NotFoundHttpException if the model cannot be found
   */
  protected function findModel($id)
  {
    if (($model = Pages::findOne($id)) !== null) {
      return $model;
    }

    throw new NotFoundHttpException('The requested page does not exist.');
  }
}

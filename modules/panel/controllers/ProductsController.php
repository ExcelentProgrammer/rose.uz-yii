<?php

namespace app\modules\panel\controllers;

use app\components\AccessRule;
use app\models\Dashboard;
use app\models\ProductCategories;
use app\models\User;
use Yii;
use app\models\Products;
use app\models\ProductSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

/**
 * ProductsController implements the CRUD actions for Products model.
 */
class ProductsController extends Controller
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
     * Lists all Products models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Products model.
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
     * Creates a new Products model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Products();
        $model->scenario = 'create';

        if ($model->load(Yii::$app->request->post())) {
            $categories = Yii::$app->request->post('categories');
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->file) {
                $fileName = Yii::$app->user->id . '_' . time() . '.' . $model->file->extension;
                $savePath = Yii::getAlias('@uploads') . '/products/' . $fileName;
                if ($model->file->saveAs($savePath)) {
                    Dashboard::cropImage($savePath, $savePath, 500, 500);
                    $model->photo = $fileName;
                }
            }
            if ($model->save()) {
                foreach ($categories as $cat) {
                    $c = new ProductCategories();
                    $c->cat_id = $cat;
                    $c->product_id = $model->id;
                    $c->save();
                }
                Yii::$app->session->setFlash('success', 'Запись сохранена!');
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка! ' . reset($model->firstErrors));
            }
            return $this->goBack();
        }
        return $this->render('create', ['model' => $model]);
    }

    /**
     * Updates an existing Products model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $categories = Yii::$app->request->post('categories');
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->file) {
                $fileName = Yii::$app->user->id . '_' . time() . '.' . $model->file->extension;
                $savePath = Yii::getAlias('@uploads') . '/products/' . $fileName;
                if ($model->file->saveAs($savePath)) {
                    Dashboard::cropImage($savePath, $savePath, 500, 500);
                    $model->photo = $fileName;
                }
            }
            if ($model->save()) {
                ProductCategories::deleteAll(['product_id' => $model->id]);
                foreach ($categories as $cat) {
                    $c = new ProductCategories();
                    $c->cat_id = $cat;
                    $c->product_id = $model->id;
                    $c->save();
                }
                Yii::$app->session->setFlash('success', 'Запись сохранена!');
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка!');
            }
            return $this->redirect(['index']);
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Deletes an existing Products model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        ProductCategories::deleteAll(['product_id' => $model->id]);

        if ($model->delete())
            Yii::$app->session->setFlash('success', 'Запись сохранена!');
        else
            Yii::$app->session->setFlash('error', 'Ошибка!');


        return $this->redirect(['index']);
    }

    /**
     * Finds the Products model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Products the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Products::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}

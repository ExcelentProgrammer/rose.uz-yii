<?php

namespace app\controllers;

use app\models\Orders;
use Yii;
use yii\db\mssql\PDO;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;

class ApiController extends Controller
{
  const KEY = 'RN9p@?2VZH&LnhfCM6dBY3M+5u6tCvfkRXz9krHT$^4RyHHnLhRJ&@YfamK_f#3KKNwn_eCv--w@4dXSU25Ypz6akx5kWtCQdbqRqV@SwExAMu9^qQM+^Ngrpxp@YKbC_RXKZay^cm_gavkzv_wkFzVPDvE4RNRGpxEXQD*yEDkagkK3D#u*LsJD6jJ3PU!u_QS@Az7F?&!$Z58XXE=Qy=V9N4&K7^m=&HnuVxrRspc#ZBEeZYLjg!CXc*&8SY*Y';

  public function beforeAction($action)
  {
    $this->enableCsrfValidation = false;
    return parent::beforeAction($action);
  }

  private function checkRequest()
  {
    $r = [];
    $r['access'] = true;
    $key = Yii::$app->request->post('key');

    if ($key !== self::KEY) {
      $response['state'] = "ERROR";
      $response['stateCode'] = 403;
      $response['message'] = "Недопустимые параметры авторизации!";
      $r['access'] = false;
      $r['response'] = $response;
    }
    return $r;
  }

  public function actionGetCategories()
  {
    $response = [];
    Yii::$app->response->format = Response::FORMAT_JSON;

    $check = $this->checkRequest();
    if (!$check['access'])
      return $check['response'];

    $categories = Yii::$app->db->createCommand("select id, name,position from category where hidden=0")->queryAll();

    $response['state'] = "OK";
    $response['stateCode'] = 200;
    $response['categories'] = $categories;
    return $response;
  }

  public function actionGetProductCategories()
  {
    $response = [];
    Yii::$app->response->format = Response::FORMAT_JSON;

    $check = $this->checkRequest();
    if (!$check['access'])
      return $check['response'];

    $categories = Yii::$app->db->createCommand("select cat_id, product_id from product_categories")->queryAll();

    $response['state'] = "OK";
    $response['stateCode'] = 200;
    $response['categories'] = $categories;
    return $response;
  }

  public function actionGetProducts()
  {
    $response = [];
    Yii::$app->response->format = Response::FORMAT_JSON;

    $check = $this->checkRequest();
    if (!$check['access'])
      return $check['response'];

    $photoUrl = Url::base(true) . '/uploads/products/';
    $products = Yii::$app->db->createCommand("select id,name,description,concat('{$photoUrl}',photo) as photo,price,recommend from products where hidden=0")->queryAll();

    $response['state'] = "OK";
    $response['stateCode'] = 200;
    $response['products'] = $products;
    return $response;
  }

  public function actionGetRates()
  {
    $response = [];
    Yii::$app->response->format = Response::FORMAT_JSON;

    $check = $this->checkRequest();
    if (!$check['access'])
      return $check['response'];

    $response['state'] = "OK";
    $response['stateCode'] = 200;
    $response['rates'] = [
      'RUB' => Orders::RUB_RATE,
      'USD' => Orders::USD_RATE,
    ];

    return $response;
  }

  public function actionGetStates()
  {
    $response = [];
    Yii::$app->response->format = Response::FORMAT_JSON;

    $check = $this->checkRequest();
    if (!$check['access'])
      return $check['response'];

    $response['state'] = "OK";
    $response['stateCode'] = 200;
    $response['states'] = Orders::$statuses;

    return $response;
  }

  public function actionGetOrderStatus()
  {
    $response = [];
    Yii::$app->response->format = Response::FORMAT_JSON;

    $check = $this->checkRequest();
    if (!$check['access'])
      return $check['response'];

    $id = Yii::$app->request->post('id');
    $model = Orders::find()->where('id=:id and state>=0', [':id' => $id])->one();

    $response['state'] = "OK";
    $response['stateCode'] = 200;
    $response['data'] = [
      'stateCode' => $model->state,
      'state' => Orders::$statuses[$model->state],
    ];

    return $response;
  }


}

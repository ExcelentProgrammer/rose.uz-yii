<?php

namespace app\controllers;

use app\models\Dashboard;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Cookie;

class SiteController extends Controller
{
  /**
   * @inheritdoc
   */

  public $enableCsrfValidation = false;

  /* public function behaviors()
  {
    return [
      'access' => [
        'class' => AccessControl::className(),
        'only' => ['logout'],
        'rules' => [
          [
            'actions' => ['logout'],
            'allow' => true,
            'roles' => ['@'],
          ],
        ],
      ],
      'verbs' => [
        'class' => VerbFilter::className(),
        'actions' => [
          'logout' => ['post'],
        ],
      ],
    ];
  } */

public function behaviors()     {
    $behaviors = parent::behaviors(); 

    // remove authentication filter         
    unset($behaviors['authenticator']);   

    $behaviors['corsFilter'] = [     
        'class' => \yii\filters\Cors::class,
        'cors'  => [
                'Origin' => ['*'], 
                'Access-Control-Request-Method'    => ['POST', 'GET', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'], 
                'Access-Control-Allow-Headers' => ['Origin', 'X-Requested-With', 'Content-Type', 'accept', 'Authorization'], 
                'Access-Control-Request-Headers' => ['*'],    
                'Access-Control-Max-Age'           => 3600, // Cache (seconds) 
                // Allow the X-Pagination-Current-Page header to be exposed to the browser.             
                'Access-Control-Expose-Headers' => ['X-Pagination-Total-Count', 'X-Pagination-Page-Count', 'X-Pagination-Current-Page', 'X-Pagination-Per-Page'] 
        ] 
    ];           

    $behaviors['authenticator'] = [];          

    $behaviors['access'] = [           
      'class' => AccessControl::className(),           
      'rules' => [   
                  [  
                    'allow' => true,   
                    'actions' => ['options'], // important for cors ie. pre-flight requests   
                  ], 
                ]   
    ];
  } 

  

  /**
   * @inheritdoc
   */
  public function actions()
  {
    return [
//            'error' => [
//                'class' => 'yii\web\ErrorAction',
//            ],
      'captcha' => [
        'class' => 'yii\captcha\CaptchaAction',
        'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
      ],
    ];
  }

  public function beforeAction($action)
  {
    
    $lang = Yii::$app->request->cookies->get('language');
    if (empty($lang)) {
      $lang = "ru";
    }
    Yii::$app->language = $lang;
    return parent::beforeAction($action);
  }

  public function actionError()
  {
    $this->layout = 'error';
    $exception = Yii::$app->errorHandler->exception;
    if ($exception !== null) {
      return $this->render('error', ['exception' => $exception]);
    }
  }

  public function actionIndex()
  {
    return $this->render('index');
  }

  public function actionLanguage($lang)
  {
    Yii::$app->language = $lang;

    $languageCookie = new Cookie([
      'name' => 'language',
      'value' => $lang,
      'expire' => time() + 60 * 60 * 24 * 30,
    ]);
    Yii::$app->response->cookies->add($languageCookie);
    return $this->redirect(Yii::$app->request->referrer ?: Yii::$app->homeUrl);
  }

  public function actionOffer()
  {
    $this->layout = 'clear';
    return $this->render('offer');
  }

  public function actionComplete($system)
  {
    $type = "Yandex Money";
    if ($system == 'wm')
      $type = "Web Money";
      if ($system == 'pb')
          $type = "Paybox";

      if($type == 'Paybox'){
          return $this->render('complete');
      }
    Dashboard::sendNotification("Rose.uz: Оплата через {$type}.");
    return $this->render('complete');
  }

  public function actionFail()
  {
    return $this->render('fail');
  }

  public function actionSetCurrency($id)
  {
//        $respCookie = Yii::$app->response->cookies;
//        if ($id < 4) {
//            $respCookie->add(new Cookie([
//                'expire' => time() + 30 * 24 * 3600,
//                'name' => 'currency',
//                'value' => $id,
//            ]));
//        }
    $session = Yii::$app->session;
    $session->set('currency', $id);
    return $this->goBack();
  }

  public function actionTest()
  {
    $s = 'a:11:{s:14:"click_trans_id";s:8:"14566027";s:10:"service_id";s:4:"8773";s:17:"merchant_trans_id";s:5:"12351";s:19:"merchant_prepare_id";s:2:"45";s:6:"amount";s:3:"500";s:6:"action";s:1:"1";s:5:"error";s:1:"0";s:10:"error_note";s:2:"Ok";s:9:"sign_time";s:19:"2019-01-04 19:53:08";s:11:"sign_string";s:32:"d23cc5abc0bf2d66483159b8c41c8322";s:15:"click_paydoc_id";s:8:"16853761";}';
    return print_r(unserialize($s));
  }
}

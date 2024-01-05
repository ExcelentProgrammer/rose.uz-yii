<?php

namespace app\modules\panel\controllers;

use app\models\Chats;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ChatsController implements the CRUD actions for Chats model.
 */
class PaymentController extends Controller
{

    public function beforeAction($action)
    {
        if ($action->id == 'paynet') {
            $this->enableCsrfValidation = false;
        }

        return parent::beforeAction($action);
    }

    public function actionClick($id)
    {
        $this->layout = 'clear';
        $model = $this->findChat($id);
        return $this->render('click', [
            'model' => $model
        ]);
    }

    public function actionPayme($id)
    {
        $this->layout = 'clear';
        $model = $this->findChat($id);
        return $this->render('payme', [
            'model' => $model
        ]);
    }

    protected function findChat($id)
    {
        if (($model = Chats::find()->where(['chat_id' => $id])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('Bunday sahifa topilmadi. Qaytadan urunib ko\'ring');
        }
    }

}

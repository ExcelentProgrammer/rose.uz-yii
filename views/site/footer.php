<?php

use app\models\Category;
use yii\helpers\Html;

$categories = Category::find()->where(['hidden' => 0])->orderBy(['position' => SORT_ASC])->all();
?>
<div class="container-fluid footer">
  <div class="container">
    <div class="row footer-inner mb20">
      <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <div class="footer-first-menu">
          <ul>
            <li><?= Html::a(Yii::t('app', 'about'), ['pages/about']) ?></li>
            <li><?= Html::a(Yii::t('app', 'delivery'), ['pages/delivery']) ?></li>
            <li><?= Html::a(Yii::t('app', 'payment'), ['pages/payment']) ?></li>
            <li><?= Html::a(Yii::t('app', 'guarantee'), ['pages/guarantees']) ?></li>
            <li><?= Html::a(Yii::t('app', 'corp'), ['pages/corp']) ?></li>
            <li><?= Html::a(Yii::t('app', 'contacts'), ['pages/contacts']) ?></li>
          </ul>
        </div>
      </div>
      <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <div class="footer-second-menu">
          <ul>
            <?php foreach ($categories as $category): ?>
              <li><?= Html::a($category->getName(), ['catalog/view', 'id' => $category->id]) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
        <div class="row">
          <div class="col-sm-12 col-md-12 col-lg-12 prl0 hidden">
            <div class="input-group footer-search-block mb30">
              <input type="text" class="form-control footer-search-field" placeholder="Поиск">
              <div class="input-group-btn">
                <button class="btn btn-default footer-search-btn" type="submit">
                  <i class="glyphicon glyphicon-search"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 prl0">
            <div class="footer-contact">
              <span class="footer-contact-label"><?= Yii::t('app', 'phone') ?></span>
              <span class="footer-contact-value">+998 71 200-9800</span>
            </div>
          </div>
          <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 prl0">
            <div class="footer-contact">
              <span class="footer-contact-label">E-mail</span>
              <span class="footer-contact-value">roseuz@yandex.ru</span>
            </div>
          </div>
          <div class="col-xs-12 col-lg-12 mb20 prl0">
            <div class="footer-contact">
              <span class="footer-contact-label"><?= Yii::t('app', 'address') ?></span>
              <a href="https://yandex.uz/maps/-/CCauRU~u" target="_blank">
                <span class="footer-contact-value"><?= Yii::t('app', 'addressValue') ?></span>
              </a>
            </div>
          </div>
          <div class="col-xs-12 col-sm-4 col-md-12 socials mb20 text-left">
            <ul class="list-inline">
              <li><a href="https://www.facebook.com/www.rose.uz/" target="_blank"><i
                    class="fa fa-2x fa-facebook-official"></i></a></li>
              <li><a href="https://www.instagram.com/rose.uz" target="_blank"><i
                    class="fa fa-2x fa-instagram"></i></a></li>
              <li><a href="https://t.me/roseuzbot" target="_blank"><i
                    class="fa fa-2x fa-telegram"></i></a></li>
              <li><a href="#"><i class="fa fa-2x fa-odnoklassniki-square fa-lg"></i></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div class="row csp">
      <div class="col-xs-12 col-sm-4 col-md-6 footer-copyright mb20">
        <span>&copy; Rose Market <?= date("Y") ?></span>
      </div>
      <div class="col-xs-12 col-sm-4 col-md-6 mb20 footer-copyright text-right">
        <span><?= Yii::t('app', 'developed') ?> <a target="_blank" href="https://botagent.uz" title="<?= Yii::t('app', 'developed') ?> Botagent"
                              class="powered-by">Botagent</a></span>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <ul class="payment-list">
          <li><img src="<?= Yii::$app->homeUrl ?>img/payme-logo.png"></li>
          <li><img src="<?= Yii::$app->homeUrl ?>img/click-logo.png"></li>
          <li><img src="<?= Yii::$app->homeUrl ?>img/visa.png"></li>
          <li><img src="<?= Yii::$app->homeUrl ?>img/mastercard.png"></li>
          <li><img src="<?= Yii::$app->homeUrl ?>img/sberbank.png"></li>
          <li><img src="<?= Yii::$app->homeUrl ?>img/yandex-money.png"></li>
          <li><img src="<?= Yii::$app->homeUrl ?>img/web-money.png"></li>
          <li><img src="<?= Yii::$app->homeUrl ?>img/qiwi_logo.png"></li>
        </ul>
      </div>
    </div>
  </div>
</div>
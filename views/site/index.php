<?php

/* @var $this yii\web\View */

use app\models\Products;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ListView;

$this->title = Yii::t('app', 'title');
$this->registerCssFile("@web/css/slick.css", ['depends' => [\yii\bootstrap\BootstrapAsset::className()]]);
$this->registerCssFile("@web/css/slick-theme.css", ['depends' => [\yii\bootstrap\BootstrapAsset::className()]]);
$this->registerJsFile('@web/js/slick.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJs(
  "$('.carousel').slick({
      infinite: false,
      slidesToShow: 3,
      speed: 300,
      slidesToScroll: 3,
      responsive: [
        {
          breakpoint: 1024, 
          settings: {
            slidesToShow: 3,
            slidesToScroll: 3,
            infinite: true,
            dots: true
          }
        },
        {
          breakpoint: 600,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 2
          }
        },
        {
          breakpoint: 480,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1
          }
        }
      ]
    });",
  View::POS_READY
);

$query = Products::find()
  ->leftJoin('product_categories', 'products.id=product_categories.product_id')
  ->leftJoin('category', 'product_categories.cat_id=category.id')
  ->where(['recommend' => 1, 'category.bot_id' => Yii::$app->params['system']])
  ->groupBy('id')
  ->limit(6);
$dataProvider = new ActiveDataProvider([
  'query' => $query,
  'pagination' => false,
]);

?>
<?= $this->render('//site/_slider') ?>
  <div class="container">
    <h1 class="block-title"><?= Yii::t('app', 'recommended') ?></h1>
    <?= ListView::widget([
      'dataProvider' => $dataProvider,
      'summary' => false,
      'itemView' => '//catalog/_list-item',
      'options' => [
        'tag' => false,
      ],
      'itemOptions' => [
        'class' => 'col-xs-12 col-sm-4 col-md-4 col-lg-4 product-item',
      ],
      'layout' => '<div class="row products-list">{items}</div><div class="products-list-pagination">{pager}</div>',
    ]); ?>

    <div class="row all-products-block">
      <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <a href="<?= Url::to('catalog') ?>">
          <div class="all-products-button"><?= Yii::t('app', 'goToCatalog') ?></div>
        </a>
      </div>
    </div>

    <div class="why-we mb30">
      <h1 class="block-title why-title"><?= Yii::t('app', 'why') ?></h1>
      <div class="row">
        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 why-we-block">
          <div class="why-we-icon text-center cp">
            <span class="fa fa-picture-o fa-3x"></span>
          </div>
          <h3><?= Yii::t('app', 'whyOneTitle') ?></h3>
          <p><?= Yii::t('app', 'whyOneValue') ?></p>
        </div>
        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 why-we-block">
          <div class="why-we-icon text-center cp">
            <span class="fa fa-handshake-o fa-3x"></span>
          </div>
          <h3><?= Yii::t('app', 'whyTwoTitle') ?></h3>
          <p><?= Yii::t('app', 'whyTwoValue') ?></p>
        </div>
        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 why-we-block">
          <div class="why-we-icon text-center cp">
            <span class="fa fa-envelope-open-o fa-3x"></span>
          </div>
          <h3><?= Yii::t('app', 'whyThreeTitle') ?></h3>
          <p><?= Yii::t('app', 'whyThreeValue') ?></p>
        </div>
      </div>
    </div>
  </div>
  <div class="container-fluid delivery-block mb30">
    <div class="container">
      <h1 class="block-title cw"><?= Yii::t('app', 'deliveryPhoto') ?></h1>
      <div class="row carousel">
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/1.jpg" class="img-responsive">
          </div>
        </div>

        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/2.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/3.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/4.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/5.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/6.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/7.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/9.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/10.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/11.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/12.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/13.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/14.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/15.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/16.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/17.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/18.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/19.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/20.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/21.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/22.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/23.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/24.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/25.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/26.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/27.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/28.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/29.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/30.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/31.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/32.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/33.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/34.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/35.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/36.jpg" class="img-responsive">
          </div>
        </div>
        <div class="col-md-4">
          <div class="delivery-photo">
            <img src="<?= Yii::$app->homeUrl ?>uploads/delivery/37.jpg" class="img-responsive">
          </div>
        </div>
      </div>
    </div>
  </div>
<?= $this->render('//site/info-block') ?>
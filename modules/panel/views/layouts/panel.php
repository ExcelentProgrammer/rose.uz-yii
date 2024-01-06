<?php

/* @var $this \yii\web\View */

/* @var $content string */

use app\assets\PanelAsset;
use app\models\Dashboard;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

PanelAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
  <meta charset="<?= Yii::$app->charset ?>">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0' name='viewport'/>
  <meta name="viewport" content="width=device-width"/>

  <?= Html::csrfMetaTags() ?>
  <title><?= Html::encode($this->title) ?> | Rose.uz</title>
  <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrapper">
  <div class="sidebar" data-color="red" data-image="<?= Yii::$app->homeUrl ?>assets/panel/img/sidebar-3.jpg">
    <div class="sidebar-wrapper">
      <div class="logo">
        <?= Html::a('Rose', '/panel', ['class' => 'simple-text']) ?>
      </div>

      <ul class="nav">
        <li class="<?= Dashboard::isNavActive('default', 'index') ? 'active' : '' ?>">
          <?= Html::a('<i class="pe-7s-graph"></i><p>Панель управления</p>', ['/panel']) ?>
        </li>
        <li class="<?= Dashboard::isNavActive('appearance') ? 'active' : '' ?>">
          <?= Html::a('<i class="pe-7s-paint"></i><p>Внешний вид</p>', ['appearance/index']) ?>
        </li>
        <li class="<?= Dashboard::isNavActive('statistics') ? 'active' : '' ?>">
          <?= Html::a('<i class="pe-7s-graph3"></i><p>Статистика</p>', ['statistics/index']) ?>
        </li>
        <li class="<?= Dashboard::isNavActive('category') ? 'active' : '' ?>">
          <?= Html::a('<i class="pe-7s-ribbon"></i><p>Категории</p>', ['category/index']) ?>
        </li>
        <li class="<?= Dashboard::isNavActive('products') ? 'active' : '' ?>">
          <?= Html::a('<i class="pe-7s-gift"></i><p>Продукты</p>', ['products/index']) ?>
        </li>
        <li class="<?= Dashboard::isNavActive('orders', 'index') ? 'active' : '' ?>">
          <?= Html::a('<i class="pe-7s-note2"></i><p>Заказы</p>', ['orders/index']) ?>
        </li>
        <li class="<?= Dashboard::isNavActive('orders', 'archive') ? 'active' : '' ?>">
          <?= Html::a('<i class="pe-7s-note2"></i><p>Архив</p>', ['orders/archive']) ?>
        </li>
        <li class="<?= Dashboard::isNavActive('pages') ? 'active' : '' ?>">
          <?= Html::a('<i class="pe-7s-display2"></i><p>Страницы</p>', ['pages/index']) ?>
        </li>
        <li class="hidden <?= Dashboard::isNavActive('telegram-users') ? 'active' : '' ?>">
          <?= Html::a('<i class="pe-7s-news-paper"></i><p>Чаты</p>', ['telegram-users/index']) ?>
        </li>
        <li class="<?= Dashboard::isNavActive('user') ? 'active' : '' ?>">
          <?= Html::a('<i class="pe-7s-id"></i><p>Пользователи</p>', ['user/index']) ?>
        </li>
        <li class="hidden <?= Dashboard::isNavActive('feedback') ? 'active' : '' ?>">
          <?= Html::a('<i class="pe-7s-umbrella"></i><p>Отзывы</p>', ['feedback/index'], ['class' => 'nav-link']) ?>
        </li>
        <li class="active-pro">
          <?= Html::a('<i class="pe-7s-rocket"></i><p>Перейти на сайт</p>', Url::base(true), ['target' => '_blank']) ?>
        </li>
      </ul>
    </div>
  </div>

  <div class="main-panel">
    <nav class="navbar navbar-default navbar-fixed">
      <div class="container-fluid">
        <div class="navbar-header">
          <?= Breadcrumbs::widget([
            'homeLink' => ['label' => "Главная", 'url' => Url::to(['/panel'])],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
          ]) ?>
          <button type="button" class="navbar-toggle" data-toggle="collapse"
                  data-target="#navigation-example-2">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav navbar-right">
            <li>
              <?= Html::a('<p>Выйти (' . Yii::$app->user->identity->username . ')</p>', ['/panel/default/logout'], ['data-method' => 'POST']) ?>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="content">
      <div class="container-fluid">
        <?= $content ?>
      </div>
    </div>

    <footer class="footer">
      <div class="container-fluid">
        <p class="copyright pull-right">
          &copy; <?= date("Y") ?> <a href="https://botagent.uz" target="_blank">Botagent</a> сделано с любовью
        </p>
      </div>
    </footer>
  </div>
</div>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

<?php

/* @var $this \yii\web\View */

/* @var $content string */

use app\assets\RoseAsset;
use yii\helpers\Html;

RoseAsset::register($this);
?>
<?php $this->beginPage() ?>
  <!DOCTYPE html>
  <html lang="<?= Yii::$app->language ?>">
  <head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Cache-Control" content="public">
    <meta name="author" content="Азим Махмудов"/>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description"
          content="<?= Yii::t('app', 'description') ?>">
    <meta name="keywords"
          content="<?= Yii::t('app', 'keywords') ?>">

    <meta name="yandex-verification" content="c0b0dfb0a16b331d"/>
    <meta name="google-site-verification" content="MLNcrXekuznUVv6NF_fASzUQw_YgYZ3bNeP2HRGM6Qc"/>

    <meta name="robots" content="index, follow"/>

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Rose"/>
    <meta property="og:title" content="<?= Yii::t('app', 'title') ?> | Rose.uz"/>
    <meta property="og:url" content="https://rose.uz"/>
    <meta property="og:image" content="https://rose.uz/img/header.jpg"/>
    <meta property="og:description"
          content="<?= Yii::t('app', 'description') ?>"/>

    <link href="<?= Yii::$app->homeUrl ?>favicon.ico" rel="icon"/>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">

    <?= Html::csrfMetaTags() ?>

    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-109836759-9"></script>
    <script>
      window.dataLayer = window.dataLayer || []

      function gtag () {dataLayer.push(arguments)}

      gtag('js', new Date())
      gtag('config', 'UA-109836759-9')
    </script>
    <!-- <script src="//code.jivosite.com/widget/MPS1CpJstg" async></script> -->
    <script type="text/javascript">
        window.$crisp=[];window.CRISP_WEBSITE_ID="1306804e-4936-461a-b7e6-92891ade6dff";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();
        // let icon = document.querySelector(".cc-nsge");
        // let messageBox = document.querySelector(".cc-nsge");

        // icon.style.left = "15px!important";
        // messageBox.style.left = "20px!important";
    </script>

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <title><?= Html::encode($this->title) ?> | Rose.uz</title>
    <?php $this->head() ?>
  </head>
  <body>
  <?php $this->beginBody() ?>
  <?= $this->render('//site/header') ?>
  <?= $this->render('//site/menu') ?>
  <?= $content ?>

  <div class="callback-btn-area">
    <div class="callback-btn-container" data-toggle="modal" data-target="#callbackModal">
      <img class="callback-btn" src="<?= Yii::$app->homeUrl ?>img/callback.svg" width="100px" height="100px">
    </div>
  </div>
  <div id="callbackModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header" style="background: #EE3963;color: white">
          <h4 class="modal-title" style="text-align: center"><?= Yii::t('app', 'callbackInfo') ?></h4>
        </div>
        <div class="modal-body">
          <div class="callback-modal-container">
            <div class="row">
              <div class="col-md-offset-2 col-md-8">
                <div class="input-group">
                  <input id="callback-number" type="text" class="form-control"
                         placeholder="Номер телефона">
                  <span class="input-group-btn">
                                        <button class="btn btn-success" type="button"
                                                data-act="callback"><i class="fa fa-phone fa-lg" aria-hidden="true"></i></button>
                                    </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
  <?= $this->render('//site/footer') ?>
  <!-- Yandex.Metrika counter -->
  <script type="text/javascript">
    (function (m, e, t, r, i, k, a) {
      m[i] = m[i] || function () {(m[i].a = m[i].a || []).push(arguments)}
      m[i].l = 1 * new Date()
      k = e.createElement(t), a = e.getElementsByTagName(t)[0], k.async = 1, k.src = r, a.parentNode.insertBefore(k, a)
    })
    (window, document, 'script', 'https://mc.yandex.ru/metrika/tag.js', 'ym')

    ym(52406740, 'init', {
      id: 52406740,
      clickmap: true,
      trackLinks: true,
      accurateTrackBounce: true
    })
  </script>
  <noscript>
    <div><img src="https://mc.yandex.ru/watch/52406740" style="position:absolute; left:-9999px;" alt=""/></div>
  </noscript>
  <!-- /Yandex.Metrika counter -->
  <?php $this->endBody() ?>
  </body>
  </html>
<?php $this->endPage() ?>
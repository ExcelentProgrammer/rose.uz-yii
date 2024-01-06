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
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?= Html::csrfMetaTags() ?>
  <!-- Global site tag (gtag.js) - Google Analytics -->
  <script async src="https://www.googletagmanager.com/gtag/js?id=UA-109836759-9"></script>
  <script>
    window.dataLayer = window.dataLayer || []

    function gtag () {
      dataLayer.push(arguments)
    }

    gtag('js', new Date())
    gtag('config', 'UA-109836759-9')
  </script>

  <title><?= Html::encode($this->title) ?></title>
  <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
  <div class="content">
    <?= $content ?>
  </div>
</div>

<footer class="footer">
  <div class="container">
    <p class="center">&copy; Botagent <?= date('Y') ?></p>
  </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

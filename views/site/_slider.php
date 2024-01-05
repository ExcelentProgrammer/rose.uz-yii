<div class="container-fluid mb30" style="position: relative">
  <div class="slider">
    <div id="layerslider" style="width:100%;height:500px;">
    <div class="ls-slide" data-ls="slidedelay:4000;timeshift:-1000;">
        <img src="<?= Yii::$app->homeUrl ?>img/rose_bg.jpg" class="ls-bg" alt="Slide background"/>
        <div class="ls-l l0"
             style="top:150px;left:0;font-size:20px; width: 100%; text-align: center; font-family: Lato, 'Open Sans', sans-serif; padding-top: 20px; padding-bottom: 20px; font-weight:normal;color:white; background: #bf2338; margin:0;">
          <h1>Плановое техническое обслуживание! Во время обновлении сайта, система оплаты не будет работать. Оформите заказ через наши соц. сети или контакты</h1>
        </div>
      </div>  
    <div class="ls-slide" data-ls="slidedelay:4000;timeshift:-1000;">
        <img src="<?= Yii::$app->homeUrl ?>img/rose_bg.jpg" class="ls-bg" alt="Slide background"/>
        <div class="ls-l l0"
             style="top:150px;left:0;font-size:20px; width: 100%; text-align: center; font-family: Lato, 'Open Sans', sans-serif; font-weight:normal;color:white; padding-top: 20px; padding-bottom: 20px; background: #bf2338; margin:0;">
          <h1>Мы обновляемся! Приносим извинения за предоставленные неудобства</h1>
        </div>
    </div>
    <div class="ls-slide" data-ls="slidedelay:4000;timeshift:-1000;">
        <img src="<?= Yii::$app->homeUrl ?>img/rose_bg.jpg" class="ls-bg" alt="Slide background"/>
        <div class="ls-l l0"
             style="top:150px;left:0;font-size:20px; width: 100%; text-align: center; font-family: Lato, 'Open Sans', sans-serif; font-weight:normal;color:white; padding-top: 20px; padding-bottom: 20px; background: #bf2338; margin:0;">
          <h1>Оформить заказ вы можете через наш Instagram, Telegram или позвонив нам</h1>
        </div>
    </div>
    </div>
  </div>
</div>
<style>
    .l0 {
        left: 0 !important;
    }
</style>

<?php
$url = Yii::$app->homeUrl;
$js = <<<JS
    $("#layerslider").layerSlider({
        responsive: false,
        responsiveUnder: 1280,
        layersContainer: 1280,
        hoverPrevNext: false,
        skinsPath: '{$url}css/skins/'
    });
JS;
$this->registerJs($js);
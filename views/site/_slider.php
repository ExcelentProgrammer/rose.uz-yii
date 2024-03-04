<div class="container-fluid mb30" style="position: relative">
    <style>
        @media (max-width: 720px) {
            .banner-text {
                font-size: 15px !important;
            }
            .main-banner{
                height: 200px !important;
            }
        }
        @media (max-width: 1024px) {
            .banner-text {
                font-size: 20px;
            }
        }
    </style>
    <?php
    $message_1 = ' <div class="ls-l l0" style="top:10px;left:0;font-size:20px; width: 100%; text-align: center; font-family: Lato, \'Open Sans\', sans-serif; padding-top: 20px; padding-bottom: 20px; font-weight:normal;color:white; background: #bf2338; margin:0;"><h1 class="banner-text"">зникновения проблем во время бронирования просьба обращаться на телеграм или ватсапп +998977031020. С уважением, Ваш Rose.uz</h1></div>';
    $message_2 = '  <div class="ls-l l0" style="top:10px;left:0;font-size:20px; width: 100%; text-align: center; font-family: Lato, \'Open Sans\', sans-serif; font-weight:normal;color:white; padding-top: 20px; padding-bottom: 20px; background: #bf2338; margin:0;"><h1 class="banner-text">Диккат! 8 март учун буюртмалар кабул килинмокда! Сайтдаги нархлар ва махсулотлар буюртмалар учун тайёр! Буюртмалар 5 март куни 12:00 гача кабул килинади! Сайтда буюртма бериш чогида саволлар булса +998977031020 ракамларига телеграм ёки Ватсапп оркали мурожаат килишингиз мумкин! Хурмат билан, Rose.uz жамоаси!</h1></div>'
    ?>
    <div class="slider">
        <div id="layerslider" style="width:100%;height:500px;" class="main-banner">
            <div class="ls-slide" data-ls="slidedelay:4000;timeshift:-1000;">
                <img src="<?= Yii::$app->homeUrl ?>img/rose_bg.jpg" class="ls-bg" alt="Slide background"/>
                <?= $message_1 ?>
            </div>
            <div class="ls-slide" data-ls="slidedelay:4000;timeshift:-1000;">
                <img src="<?= Yii::$app->homeUrl ?>img/rose_bg.jpg" class="ls-bg" alt="Slide background"/>
                <?= $message_2 ?>
            </div>
            <div class="ls-slide" data-ls="slidedelay:4000;timeshift:-1000;">
                <img src="<?= Yii::$app->homeUrl ?>img/rose_bg.jpg" class="ls-bg" alt="Slide background"/>
                <?= $message_1 ?>
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
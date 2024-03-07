<div class="container-fluid mb30" style="position: relative">
    <style>
        @media (max-width: 720px) {
            .banner-text {
                font-size: 15px !important;
            }

            .main-banner {
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
    $html = '<div class="ls-l l0" style="top:10px;left:0;font-size:20px; width: 100%; text-align: center; font-family: Lato, \'Open Sans\', sans-serif; padding-top: 20px; padding-bottom: 20px; font-weight:normal;color:white; background: #bf2338; margin:0;"><h1 class="banner-text">jscorp_text</h1></div>';
    $message_1 = str_replace("jscorp_text",
        "Diqqat! 7 mart uchun buyurtmalar qabul 
qilinmoqda! Saytagi narxlar va maxsulotlar buyurtmalar uchun tayyor! Buyurtmalar
6 mart kuni 23:59 gacha qabul qilinadi! Saytga buyurtma berish chog'ida savolar
 bulsa +998977031020 raqamlariga telegram yoki whatsapp orqali murojaat qilishingiz mumkin!
  Xurmat bilan Rose.uz jamoasi!", $html);
    $message_2 = str_replace("jscorp_text",
        "Диққат! 7 март учун буюртмалар қабул қилинмоқда! 
Saytdagi narxlar va mahsulotlar buyurtmalar uchun tayyor! Buyurtmalar 
6 mart kuni 23:59 gacha qabul qilinadi! Saytda buyurtma berish chog'ida
 savollar bo'lsa +998977031020 raqamlariga telegram yoki whatsapp orqali
  murojaat qilishingiz mumkin! Hurmat bilan, Rose.uz jamoasi!", $html);
    $message_3 = str_replace("jscorp_text",
        "Внимание! Принимаем предзаказы ко дню 7 марта! Весь ассортимент 
и цены на продукцию актуальны до 23:59 часов дня 6 марта. В случае возникновения 
проблем во время бронирования просьба обращаться на телеграм или ватсапп +998977031020.
 С уважением, Ваш Rose.uz",
        $html);
    ?>

    <div class="slider">
        <div id="layerslider" style="width:100%;height:500px;" class="main-banner">
            <div class="ls-slide" data-ls="slidedelay:4000;timeshift:-1000;">
                <img src="<?= Yii::$app->homeUrl ?>img/rose_bg.jpg" class="ls-bg" alt="Slide background"/>
                <?= $message_3 ?>
            </div>
            <div class="ls-slide" data-ls="slidedelay:4000;timeshift:-1000;">
                <img src="<?= Yii::$app->homeUrl ?>img/rose_bg.jpg" class="ls-bg" alt="Slide background"/>
                <?= $message_1 ?>
            </div>
            <div class="ls-slide" data-ls="slidedelay:4000;timeshift:-1000;">
                <img src="<?= Yii::$app->homeUrl ?>img/rose_bg.jpg" class="ls-bg" alt="Slide background"/>
                <?= $message_2 ?>
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
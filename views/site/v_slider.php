<div class="container-fluid mb30" style="position: relative">
    <video id="video" width="1280" height="720" controls autoplay>
        <source src="<?= Yii::$app->homeUrl ?>video.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
</div>

<?php
$url = Yii::$app->homeUrl;
$js = <<<JS
document.getElementById('video').play();
JS;
$this->registerJs($js);
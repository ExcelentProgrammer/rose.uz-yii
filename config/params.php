<?php
Yii::setAlias('@web', realpath(dirname(__FILE__) . '/../web/'));
Yii::setAlias('@uploads', realpath(dirname(__FILE__) . '/../web/uploads'));
Yii::setAlias('@cache', realpath(dirname(__FILE__) . '/../web/data/cache/'));

return [
    'adminEmail' => 'admin@example.com',
    'imgUrl' => 'http://rose.uz',
    'system' => 2,
];

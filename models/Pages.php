<?php

namespace app\models;

use yii\db\ActiveRecord;

class Pages extends ActiveRecord
{
    public $file;

    /**
     * Get page locale text
     *
     * @return string
     */
    function getText(): string
    {
        return $this->text_ru;
    }

}
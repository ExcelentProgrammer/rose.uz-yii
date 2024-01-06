<?php

namespace app\components;


class Request extends \yii\web\Request
{
    public $web;

    public function getBaseUrl()
    {
        return str_replace($this->web, "", parent::getBaseUrl());
    }
}
<?php

namespace app\models;

/**
 * This is the model class for Telegram Chat.
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $username
 * @property string $language_code
 *
 */
class TelegramSender
{
    public $id;
    public $first_name;
    public $last_name;
    public $username;
    public $type;

    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->first_name = $data['first_name'];
        $this->last_name = $data['last_name'];
        $this->username = $data['username'];
        $this->language_code = $data['language_code'];
    }

}
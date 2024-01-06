<?php

namespace app\models;

/**
 * This is the model class for Telegram Chat.
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $username
 * @property string $type
 *
 */
class TelegramChat
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
        $this->type = $data['type'];
    }

    public function setPhoneNumber($phone_number)
    {
        $model = Chats::findOne(['chat_id' => $this->id]);
        if ($model == null)
            return false;
        $model->phone = $phone_number;
        $model->save();
        return true;
    }

    public function getPhone()
    {
        $model = Chats::findOne(['chat_id' => $this->id]);
        if ($model == null && !empty($model->phone))
            return null;
        $model->phone;
    }

}
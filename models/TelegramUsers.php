<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "telegram_users".
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $username
 * @property string $chat_type
 * @property string $lang
 * @property string $phone
 * @property integer $is_bot
 * @property integer $is_admin
 */
class TelegramUsers extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'telegram_users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['is_bot', 'is_admin'], 'integer'],
            [['first_name', 'last_name', 'username'], 'string', 'max' => 150],
            [['chat_type'], 'string', 'max' => 15],
            [['lang'], 'string', 'max' => 7],
            [['phone'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Чат ID',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'username' => 'Имя пользователя',
            'chat_type' => 'Тип',
            'lang' => 'Язык',
            'phone' => 'Телефон',
            'is_bot' => 'Бот',
            'is_admin' => 'Админ',
        ];
    }
}

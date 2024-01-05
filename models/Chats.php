<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "chats".
 *
 * @property integer $id
 * @property integer $chat_id
 * @property string $first_name
 * @property string $last_name
 * @property string $username
 * @property string $phone
 * @property string $type
 * @property string $lang
 * @property integer $radio
 *
 * @property TelegramMessages[] $messages
 */
class Chats extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chats';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['chat_id'], 'required'],
            [['chat_id'], 'integer'],
            [['first_name', 'last_name', 'username'], 'string', 'max' => 150],
            [['type', 'phone'], 'string', 'max' => 45],
            [['lang'], 'string', 'max' => 5],
            [['chat_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'chat_id' => 'Чат ID',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'username' => 'Имя пользователя',
            'type' => 'Тип',
            'lang' => 'Язык',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(TelegramMessages::className(), ['chat_id' => 'chat_id']);
    }

    public function setLanguage($lang)
    {
        $this->lang = $lang;
        $this->save();
    }

    public static function getChats()
    {
        return ArrayHelper::map(self::find()->orderBy(['first_name' => SORT_ASC])->all(), 'chat_id', 'first_name');
    }

    public static function getName($id)
    {
        $model = self::find()->where(['chat_id' => $id])->one();
        return $model->first_name . ' ' . $model->last_name;
    }
}

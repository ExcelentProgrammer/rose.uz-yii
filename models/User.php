<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;


/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property integer $state
 * @property integer $role
 * @property string $username
 * @property string $password
 * @property string $fullname
 * @property string $phone
 * @property string $email
 * @property string $photo
 * @property string $authKey
 * @property string $accessToken
 * @property string $regDate
 * @property string $lastVisit
 *
 */
class User extends ActiveRecord implements IdentityInterface
{
    const ROLE_ADMIN = 1;
    const ROLE_MODER = 2;
    const ROLE_USER = 3;

    public static $roles = [
        1 => 'Администратор',
        2 => 'Модератор',
        3 => 'Пользователь',
    ];

    public static $states = [0 => 'Актив', 1 => 'Блок'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['role', 'username', 'password'], 'required'],
            [['state', 'role'], 'integer'],
            [['regDate', 'lastVisit'], 'safe'],
            [['username', 'email'], 'string', 'max' => 30],
            [['password'], 'string', 'max' => 150],
            [['fullname', 'phone'], 'string', 'max' => 50],
            [['authKey', 'accessToken'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'state' => 'Статус',
            'role' => 'Роль',
            'username' => 'Пользователь',
            'password' => 'Пароль',
            'fullname' => 'ФИО',
            'address' => 'Адрес',
            'phone' => 'Телефон',
            'email' => 'E-mail',
            'photo' => 'Изображение',
            'authKey' => 'Код авторизации',
            'accessToken' => 'Токен',
            'regDate' => 'Дата регистрации',
            'lastVisit' => 'Последний визит',
        ];
    }

    /**
     * Finds an identity by the given ID.
     * @param string|integer $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        // TODO: Implement findIdentity() method.
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
        return static::findOne(['accessToken' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param  string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    public function getIsAdmin()
    {
        return $this->role == self::ROLE_ADMIN;
    }

    public function getIsModer()
    {
        return $this->role == self::ROLE_MODER;
    }

    public function getIsUser()
    {
        return $this->role == self::ROLE_USER;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }

    public function generatePassword($password)
    {
        return Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        return Yii::$app->security->generateRandomString();
    }

    public function generateAccessToken()
    {
        return Yii::$app->security->generateRandomString();
    }
}

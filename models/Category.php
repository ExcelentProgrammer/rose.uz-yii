<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "category".
 *
 * @property integer $id
 * @property integer $bot_id
 * @property string $name
 * @property integer $position
 *
 * @property Bots $bot
 * @property ProductCategories[] $products
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['bot_id', 'name'], 'required'],
            [['bot_id', 'position'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['bot_id'], 'exist', 'skipOnError' => true, 'targetClass' => Bots::className(), 'targetAttribute' => ['bot_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'bot_id' => 'Бот',
            'name' => 'Название',
            'position' => 'Позиция',
        ];
    }

    public function getName() {
        return $this->name;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBot()
    {
        return $this->hasOne(Bots::className(), ['id' => 'bot_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(ProductCategories::className(), ['cat_id' => 'id']);
    }

    public static function getList()
    {
        return ArrayHelper::map(self::find()->all(), 'id', 'name');
    }

    public static function getHierarchy()
    {
        $options = [];
        $bots = Bots::find()->all();
        foreach ($bots as $bot) {
            $child_options = [];
            foreach ($bot->categories as $category) {
                $child_options[$category->id] = $category->name;
            }
            $options[$bot->name] = $child_options;
        }
        return $options;
    }

    public static function isActive($active)
    {
        $id = Yii::$app->request->get("id");
        if (Yii::$app->controller->id == 'catalog' && Yii::$app->controller->action->id == 'view' && $id == $active)
            return true;
        return false;
    }
}

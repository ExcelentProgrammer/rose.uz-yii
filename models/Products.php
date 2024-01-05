<?php

namespace app\models;

use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "products".
 *
 * @property integer $id
 * @property integer $category_id
 * @property string $name
 * @property string $description
 * @property string $photo
 * @property string $price
 * @property integer $recommend
 * @property integer $hidden
 *
 * @property ProductCategories[] $categories
 */
class Products extends \yii\db\ActiveRecord
{
    public $file;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'products';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'price'], 'required'],
            [['file'], 'required', 'on' => 'create'],
            [['category_id', 'recommend', 'hidden'], 'integer'],
            [['description'], 'string'],
            [['price'], 'number'],
            [['name'], 'string', 'max' => 255],
            [['photo'], 'string', 'max' => 50],
            [['file'], 'file'],
//            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Категория',
            'name' => 'Названия',
            'description' => 'Описания',
            'photo' => 'Фото',
            'file' => 'Фото (500x500)',
            'price' => 'Цена',
            'recommend' => 'Рекомендуем',
            'hidden' => 'Скрыть',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(ProductCategories::className(), ['product_id' => 'id']);
    }

    public function getName() {
        return $this->name;
    }

    public function getCategoriesId()
    {
        $arr = [];
        foreach ($this->categories as $category) {
            $arr[] = $category->cat_id;
        }
        return $arr;
    }

    public function getCategoriesList()
    {
        $list = "";
        foreach ($this->categories as $category) {
            $list .= $category->category->name . ", ";
        }
        return substr($list, 0, -2);
    }

    public function getSumPrice()
    {
        return $this->price;
    }

    public function getRublePrice()
    {
        return round($this->price / Orders::RUB_RATE);
    }

    public function getUsdPrice()
    {
        return round($this->price / Orders::USD_RATE);
    }

    public function getPrice()
    {
        $currency = Orders::getCurrency();
        $price = 0;
        if ($currency == Orders::CUR_SUM)
            $price = $this->getSumPrice();
        elseif ($currency == Orders::CUR_RUB)
            $price = $this->getRublePrice();
        elseif ($currency == Orders::CUR_USD)
            $price = $this->getUsdPrice();

        return $price;
    }
}

<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Orders;

/**
 * OrderSearch represents the model behind the search form about `app\models\Orders`.
 */
class OrderSearch extends Orders
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'chat_id', 'system_id', 'know_address', 'add_card', 'take_photo', 'state'], 'integer'],
            [['client_type', 'date', 'sender_name', 'sender_phone', 'sender_email', 'receiver_name', 'receiver_phone', 'delivery_date', 'receiver_address', 'card_text'], 'safe'],
            [['delivery_price', 'total_paid'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Orders::find()->where('state>=0');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => ['defaultOrder' => ['date' => SORT_DESC]],
            'pagination' => [
                'pageSize' => 50
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'chat_id' => $this->chat_id,
            'system_id' => $this->system_id,
            'date' => $this->date,
            'delivery_date' => $this->delivery_date,
//            'delivery_price' => $this->delivery_price,
            'total_paid' => $this->total_paid,
            'know_address' => $this->know_address,
            'add_card' => $this->add_card,
            'take_photo' => $this->take_photo,
            'state' => $this->state,
        ]);

        $query->andFilterWhere(['like', 'client_type', $this->client_type])
            ->andFilterWhere(['like', 'sender_name', $this->sender_name])
            ->andFilterWhere(['like', 'sender_phone', $this->sender_phone])
            ->andFilterWhere(['like', 'sender_email', $this->sender_email])
            ->andFilterWhere(['like', 'receiver_name', $this->receiver_name])
            ->andFilterWhere(['like', 'receiver_phone', $this->receiver_phone])
            ->andFilterWhere(['like', 'receiver_address', $this->receiver_address])
            ->andFilterWhere(['like', 'card_text', $this->card_text]);

        return $dataProvider;
    }
}

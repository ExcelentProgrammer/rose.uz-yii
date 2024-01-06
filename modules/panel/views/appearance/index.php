<?php
use yii\helpers\Html;

$this->title = "Внешний вид";
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="header clearfix">
                <h2 class="pull-left title">Баннерный карусель</h2>
                <p class="pull-right">
                    <?= Html::a('Добавить', ['create'], ['class' => 'btn btn-success btn-fill']) ?>
                </p>
            </div>
            <hr />
            
            <?php Pjax::begin(['timeout' => false, 'id' => 'pjax-gridview']); ?>
    <div class="table-responsive bots-index">
      <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
          'class' => 'table table-hover',
        ],
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],
          [
            'class' => 'yii\grid\SerialColumn',
            'options' => [
              'width' => 40,
              'class' => 'text-center'
            ]
          ],
//                            [
//                                'attribute' => 'id',
//                                'contentOptions' => [
//                                    'width' => '80'
//                                ]
//                            ],
//                            [
//                                'attribute' => 'bot_id',
//                                'value' => function ($model) {
//                                    return $model->bot->name;
//                                },
//                                'filter' => \app\models\Bots::getList()
//                            ],
          'title',
          'background',
          [
            'attribute' => 'background',
            'format' => 'html',
            'value' => function ($model) {
                return Html::img(Yii::getAlias('@upload') . '/background/' . $model->background, ['width' => '100px']);
            },
          ],

          [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{delete}',
            'contentOptions' => [
              'width' => 60,
              'class' => 'text-center'
            ]
          ],
        ],
      ]); ?>
    </div>
    <?php Pjax::end(); ?>
            
        </div>
    </div>
</div>
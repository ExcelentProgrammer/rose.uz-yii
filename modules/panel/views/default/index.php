<?php

/* @var $this yii\web\View */
/* @var $month integer */
/* @var $year integer */

$this->title = "Панель управления";
$this->params['breadcrumbs'][] = $this->title;

use app\models\Dashboard;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$todayStats = Dashboard::getTodayStat();
$todayValue = implode(',', $todayStats);

$monthOverview = Dashboard::getMonthStat($month, $year);
$mnd = implode(',', array_values($monthOverview[0]));
$mcd = implode(',', array_values($monthOverview[1]));
$mad = implode(',', array_values($monthOverview[2]));
$mk = implode(',', array_keys($monthOverview[0]));

$yearStats = Dashboard::getYearStat($year);
$ynd = implode(',', array_values($yearStats[0]));
$ycd = implode(',', array_values($yearStats[1]));
$yad = implode(',', array_values($yearStats[2]));
$yk = '';
foreach (Dashboard::$months as $m) {
    $yk .= '"' . $m . '",';
}
$yk = substr($yk, 0, -1);

$js = <<<JS
var chartColors = {
    red: 'rgb(255, 99, 132)',
    orange: 'rgb(255, 159, 64)',
    yellow: 'rgb(255, 205, 86)',
    green: 'rgb(75, 192, 192)',
    blue: 'rgb(54, 162, 235)',
    purple: 'rgb(153, 102, 255)',
    grey: 'rgb(201, 203, 207)',
    black: 'rgb(0, 0, 0)',
    aqua: 'rgb(0, 255, 255)',
    navy: 'rgb(0, 0, 128)',
};

var pieChart = document.getElementById('pie-chart').getContext('2d');
var chart = new Chart(pieChart, {
    type: 'pie',
    data: {
        datasets: [{
            data: [
                {$todayValue}
            ],
            backgroundColor: [
                chartColors.orange,
                chartColors.red,
                chartColors.blue,
            ],
        }],
        labels: [
            'Не принятые','Отмененные','Обработанные'    
        ]
    },
    options: {
        tooltips: {
            mode: 'index',
			intersect: false
		},
        responsive: true,
        legend: {
            display: true,
            position: 'bottom'
        }
    }
});

var barChart = document.getElementById('bar-chart').getContext('2d');
var myBar = new Chart(barChart, {
    type: 'bar',
    data: {
        labels: [{$mk}],
        datasets: [
            {
                label: 'Не принятые',
                data: [{$mnd}],
                backgroundColor: chartColors.orange
            },
            {
                label: 'Отмененные',
                data: [{$mcd}],
                backgroundColor: chartColors.red
            },
            {
                label: 'Обработанные',
                data: [{$mad}],
                backgroundColor: chartColors.blue
            },
        ]
    
    },
    options: {
        tooltips: {
            mode: 'index',
			intersect: false
		},
        responsive: true,
        legend: {
            position: 'top',
        },
        title: {
            display: false,
            text: 'Количество заказов'
        },
        scales: {
            xAxes: [{
                stacked: true,
                display: true,
                scaleLabel: {
                    display: true,
                    labelString: 'Дни'
                },
            }],
            yAxes: [{
                stacked: true,
                display: true,
                scaleLabel: {
                    display: true,
                    labelString: 'Количество'
                }
            }]
        }
    }
});

var chart = new Chart('line-chart', {
    type: 'line',
    data: {
        labels: [{$yk}],
        datasets: [
            {
                label: 'Не принятые',
                data: [{$ynd}],
                fill: false,
                backgroundColor: chartColors.orange,
                borderColor: chartColors.orange,
            },
            {
                label: 'Отмененные',
                data: [{$ycd}],
                fill: false,
                backgroundColor: chartColors.red,
                borderColor: chartColors.red,
            },
            {
                label: 'Обработанные',
                data: [{$yad}],
                fill: false,
                backgroundColor: chartColors.blue,
                borderColor: chartColors.blue,
            },
        ]
    },
    options: {
        tooltips: {
            mode: 'index',
			intersect: false
		},
        responsive: true,
        elements: {
            line: {
                tension: 0.000001
            }
        },
        title: {
            display: false,
            text: ''
        },
        tooltips: {
            mode: 'index',
            intersect: false,
        },
        hover: {
            mode: 'nearest',
            intersect: true
        },
        scales: {
            xAxes: [{
                display: true,
                scaleLabel: {
                    display: true,
                    labelString: 'Месяц'
                },
            }],
            yAxes: [{
                display: true,
                scaleLabel: {
                    display: true,
                    labelString: 'Количество'
                }
            }]
        }
    }
});
JS;

$this->registerJs($js);
?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="header text-center">
                <h4 class="title">Добро пожаловать <?= Yii::$app->user->identity->fullname ?></h4>
                <p class="category"><?= User::$roles[Yii::$app->user->identity->role] ?></p>
            </div>
            <div class="content">

            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="header">
        <h4 class="title">Фильтр</h4>
        <p class="category">Выберите месяц и год</p>
    </div>
    <div class="content">
        <div class="row">
            <?php $form = ActiveForm::begin([
                'id' => 'filterForm',
                'method' => 'get',
                'action' => ['site/index']
            ]); ?>
            <div class="col-md-2 mb20">
                <?= Html::dropDownList('month', $month, Dashboard::$months, ['class' => 'form-control']) ?>
            </div>
            <div class="col-md-2 mb20">
                <?= Html::dropDownList('year', $year, Dashboard::getYears(), ['class' => 'form-control']) ?>
            </div>
            <div class="col-md-3 mb20">
                <?= Html::submitButton('Показать', ['class' => 'btn btn-primary btn-fill']) ?>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card">

            <div class="header">
                <h4 class="title">Сегодня</h4>
                <p class="category">Заказы</p>
            </div>
            <div class="content" style="padding-top: 25px; padding-bottom: 30px;">
                <canvas id="pie-chart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="header">
                <h4 class="title"><?= Dashboard::$months[$month] ?></h4>
                <p class="category">Статистика по заказам</p>
            </div>
            <div class="content">
                <canvas id="bar-chart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">

            <div class="header">
                <h4 class="title"><?= date("Y") ?> год</h4>
                <p class="category">Статистика по заказам</p>
            </div>
            <div class="content">
                <canvas id="line-chart"></canvas>
            </div>
        </div>
    </div>
</div>

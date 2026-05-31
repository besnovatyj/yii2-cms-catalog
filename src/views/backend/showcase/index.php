<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Backend\Widgets\grid\ActionColumn;
use Besnovatyj\Catalog\helpers\ShowcaseHelper;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider ActiveDataProvider */

$this->title = 'Витрины';
$this->params['breadcrumbs'][] = $this->title;
?>

<p>
    <?= Html::a('Создать витрину', ['create'], ['class' => 'btn btn-success']) ?>
</p>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'id',
        'code',
        'name',
        [
            'attribute' => 'status',
            'value' => static function ($model) {
                return ShowcaseHelper::statusLabel($model->status);
            },
            'format' => 'raw',
        ],
        'sort',
        [
            'label' => 'Элементов',
            'value' => static function ($model) {
                return count($model->items);
            },
        ],
        [
            'class' => ActionColumn::class,
            'template' => '{view} {update} {delete}',
        ],
    ],
]) ?>

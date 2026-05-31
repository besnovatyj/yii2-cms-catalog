<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Backend\Widgets\grid\ActionColumn;
use Besnovatyj\Catalog\entities\Characteristic;
use Besnovatyj\Catalog\forms\backend\search\CharacteristicSearch;
use Besnovatyj\Catalog\helpers\CharacteristicHelper;
use Besnovatyj\Backend\Widgets\pagination\LinkPager;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel CharacteristicSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = 'Characteristics';
$this->params['breadcrumbs'][] = $this->title;
?>

<p>
    <?= Html::a('Create Characteristic', ['create'], ['class' => 'btn  btn-success']) ?>
</p>

<div class="card rounded-0">
    <div class="card-header"><?= $this->title ?></div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{summary}\n{items}",
            'columns' => [
                [
                    'attribute' => 'name',
                    'value' => function (Characteristic $model) {
                        return Html::a(Html::encode($model->name), ['view', 'id' => $model->id]);
                    },
                    'format' => 'raw',
                ],
                'slug',
                [
                    'attribute' => 'type',
                    'filter' => $searchModel->typesList(),
                    'value' => function (Characteristic $model) {
                        return CharacteristicHelper::typeName($model->type);
                    },
                ],
                [
                    'attribute' => 'required',
                    'filter' => $searchModel->requiredList(),
                    'format' => 'boolean',
                ],
                ['class' => ActionColumn::class,
                    'template' => \modules\user\components\Helper::filterActionColumn(['view', 'update', 'delete',]),
                ],
            ],
        ]); ?>
    </div>
    <div class="card-footer">
        <div class="d-grid gap-2">
            <nav aria-label="" class="nav-pagination">
                <?= LinkPager::widget([
                    'pagination' => $dataProvider->getPagination(),
                ]) ?>
            </nav>
        </div>
    </div>
</div>

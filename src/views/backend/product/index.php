<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Catalog\entities\product\Product;
use Besnovatyj\Catalog\forms\backend\search\ProductSearch;
use Besnovatyj\Catalog\helpers\ProductHelper;
use Besnovatyj\Backend\Widgets\pagination\LinkPager;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $searchModel ProductSearch */
/* @var $dataProvider ActiveDataProvider */

$this->title = 'Products';
$this->params['breadcrumbs'][] = $this->title;
?>

<p>
    <?= Html::a('Create Product', ['create'], ['class' => 'btn  btn-success']) ?>
</p>

<div class="card">
    <div class="card-header"><?= $this->title ?></div>
    <div class="card-body">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{summary}\n{items}",
            'columns' => [
                [
                    'attribute' => 'id',
                    'format' => 'raw',
                    'value' => static function (Product $model) {
                        return $model->id;
                    },
                    'contentOptions' => ['style' => 'width: 30px'],
                ],
                [
                    'value' => static function (Product $model) {
                        return $model->mainPhoto ? Html::img($model->mainPhoto->getThumbUrl('file', 'admin')) : null;
                    },
                    'format' => 'raw',
                    'contentOptions' => ['style' => 'width: 100px'],
                ],
                [
                    'attribute' => 'code',
                    'value' => static function (Product $model) {
                        return Html::a($model->code, ['view', 'id' => $model->id]);
                    },
                    'format' => 'html',
                ],
                [
                    'attribute' => 'name',
                    'value' => static function (Product $model) {
                        return Html::a(Html::decode($model->name_short ?? ''), ['view', 'id' => $model->id]);
                    },
                    'format' => 'html',
                ],
                [
                    'attribute' => 'category_id',
                    'filter' => $searchModel->categoriesList(),
                    'value' => 'category.name',
                ],
                [
                    'attribute' => 'status',
                    'filter' => $searchModel->statusList(),
                    'value' => static function (Product $model) {
                        return ProductHelper::statusLabel($model->status);
                    },
                    'format' => 'raw',
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

<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Catalog\entities\Brand;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $brand Brand */

$this->title = $brand->name;
$this->params['breadcrumbs'][] = ['label' => 'Brands', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<p>
    <?= Html::a('Update', ['update', 'id' => $brand->id], ['class' => 'btn  btn-primary']) ?>
    <?= Html::a('Delete', ['delete', 'id' => $brand->id], [
        'class' => 'btn  btn-danger',
        'data' => [
            'confirm' => 'Are you sure you want to delete this item?',
            'method' => 'post',
        ],
    ]) ?>
</p>

<div class="card">
    <div class="card-header">Common</div>
    <div class="card-body">
        <?= DetailView::widget([
            'model' => $brand,
            'attributes' => [
                'id',
                'name',
                'slug',
            ],
        ]) ?>
    </div>
</div>

<div class="card">
    <div class="card-header">SEO</div>
    <div class="card-body">
        <?= DetailView::widget([
            'model' => $brand,
            'attributes' => [
                'meta.title',
                'meta.description',
                'meta.keywords',
            ],
        ]) ?>
    </div>
</div>

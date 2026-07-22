<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Catalog\entities\Category;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $category Category */

$this->title = $category->name;
$this->params['breadcrumbs'][] = ['label' => 'Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<p>
    <?= Html::a('Update', ['update', 'id' => $category->id], ['class' => 'btn  btn-primary']) ?>
    <?= Html::a('Delete', ['delete', 'id' => $category->id], [
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
            'model' => $category,
            'attributes' => [
                'id',
                'name',
                'slug',
                'depth',
            ],
        ]) ?>
    </div>
</div>
<div class="card">
    <div class="card-header">Description</div>
    <div class="card-body">
        <?= \Besnovatyj\Shortcode\widgets\ShortcodeContent::widget(['content' => $category->description]) ?>
    </div>
</div>
<div class="card">
    <div class="card-header">SEO</div>
    <div class="card-body">
        <?= DetailView::widget([
            'model' => $category,
            'attributes' => [
                'meta.title',
                'meta.description',
                'meta.keywords',
            ],
        ]) ?>
    </div>
</div>

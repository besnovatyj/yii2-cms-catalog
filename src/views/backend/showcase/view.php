<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Catalog\assets\ShowcaseAsset;
use Besnovatyj\Catalog\entities\Characteristic;
use Besnovatyj\Catalog\entities\showcase\Showcase;
use Besnovatyj\Catalog\entities\showcase\ShowcaseItem;
use Besnovatyj\Catalog\helpers\ShowcaseHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

/* @var $this View */
/* @var $showcase Showcase */
/* @var $items ShowcaseItem[] */
/* @var $characteristics Characteristic[] */
/* @var $productsList array */

ShowcaseAsset::register($this);

$this->title = $showcase->name;
$this->params['breadcrumbs'][] = ['label' => 'Витрины', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

// Данные для TypeScript
$jsConfig = Json::encode([
    'showcaseId' => $showcase->id,
    'endpoints' => [
        'addItem' => Url::to(['/Catalog/backend/showcase/add-item']),
        'removeItem' => Url::to(['/Catalog/backend/showcase/remove-item']),
        'reorderItems' => Url::to(['/Catalog/backend/showcase/reorder-items']),
        'configureItem' => Url::to(['/Catalog/backend/showcase/configure-item']),
        'toggleItemStatus' => Url::to(['/Catalog/backend/showcase/toggle-item-status']),
        'productPhotos' => Url::to(['/Catalog/backend/showcase/product-photos']),
    ],
    'csrfToken' => Yii::$app->request->csrfToken,
    'csrfParam' => Yii::$app->request->csrfParam,
    'characteristics' => array_map(static fn(Characteristic $c) => [
        'slug' => $c->slug,
        'name' => $c->name,
    ], $characteristics),
]);

$this->registerJs("window.showcaseConfig = {$jsConfig};", View::POS_HEAD);
?>

<p>
    <?php if ($showcase->isActive()): ?>
        <?= Html::a('Draft', ['draft', 'id' => $showcase->id], ['class' => 'btn btn-primary', 'data-method' => 'post']) ?>
    <?php else: ?>
        <?= Html::a('Activate', ['activate', 'id' => $showcase->id], ['class' => 'btn btn-success', 'data-method' => 'post']) ?>
    <?php endif; ?>
    <?= Html::a('Update', ['update', 'id' => $showcase->id], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Delete', ['delete', 'id' => $showcase->id], [
        'class' => 'btn btn-danger',
        'data' => [
            'confirm' => 'Удалить витрину со всеми элементами?',
            'method' => 'post',
        ],
    ]) ?>
</p>

<div class="row mb-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Информация</div>
            <div class="card-body">
                <table class="table table-sm mb-0">
                    <tr><th>ID</th><td><?= $showcase->id ?></td></tr>
                    <tr><th>Код</th><td><code><?= Html::encode($showcase->code) ?></code></td></tr>
                    <tr><th>Название</th><td><?= Html::encode($showcase->name) ?></td></tr>
                    <tr><th>Статус</th><td><?= ShowcaseHelper::statusLabel($showcase->status) ?></td></tr>
                    <tr><th>Сортировка</th><td><?= $showcase->sort ?></td></tr>
                    <tr><th>Элементов</th><td id="showcase-items-count"><?= count($items) ?></td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Добавить товар</div>
            <div class="card-body">
                <div class="input-group">
                    <select id="showcase-product-select" class="form-select">
                        <option value="">— Выберите товар —</option>
                        <?php foreach ($productsList as $id => $name): ?>
                            <option value="<?= $id ?>"><?= Html::encode($name) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button id="showcase-add-item-btn" class="btn btn-success" type="button">
                        <i class="bi bi-plus-lg"></i> Добавить
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Список элементов витрины -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Элементы витрины</span>
        <button id="showcase-save-order-btn" class="btn btn-sm btn-outline-primary" style="display:none;">
            <i class="bi bi-arrow-down-up"></i> Сохранить порядок
        </button>
    </div>
    <div class="card-body p-0">
        <div id="showcase-items-list">
            <?php if (empty($items)): ?>
                <div class="text-muted text-center p-4" id="showcase-empty-message">
                    Витрина пуста. Добавьте товары из выпадающего списка выше.
                </div>
            <?php endif; ?>

            <?php foreach ($items as $item): ?>
                <?= $this->render('_showcase_item', [
                    'item' => $item,
                    'characteristics' => $characteristics,
                ]) ?>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Шаблон элемента витрины для JS -->
<template id="showcase-item-template">
    <?= $this->render('_showcase_item_template') ?>
</template>

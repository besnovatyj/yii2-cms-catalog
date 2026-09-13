<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Catalog\entities\Category;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * Демо-вьюха каталога (пакетный фолбэк; настоящую подачу даёт тема через overlay
 * `modules/Catalog/views/frontend/product/`). Корневые категории списком.
 *
 * @var View       $this
 * @var Category[] $categories активные корневые категории в порядке отображения
 */

$this->title = 'Каталог';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container py-4">
    <h1 class="h3 mb-4"><?= Html::encode($this->title) ?></h1>

    <?php if ($categories === []): ?>
        <p class="text-muted">Разделов пока нет.</p>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-3 g-3">
            <?php foreach ($categories as $category): ?>
                <div class="col">
                    <div class="card h-100">
                        <div class="card-body">
                            <h2 class="h5 card-title">
                                <?= Html::a(Html::encode($category->name), Url::to(['by-category', 'slug' => $category->slug]), ['class' => 'stretched-link']) ?>
                            </h2>
                            <?php if (trim((string)$category->description) !== ''): ?>
                                <p class="card-text text-muted"><?= Html::encode(strip_tags((string)$category->description)) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

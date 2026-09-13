<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use yii\bootstrap5\LinkPager;
use yii\data\DataProviderInterface;
use yii\web\View;

/**
 * Список товаров: сетка карточек и пагинация.
 *
 * @var View                  $this
 * @var DataProviderInterface $dataProvider
 */

$products = $dataProvider->getModels();
?>
<?php if ($products === []): ?>
    <p class="text-muted">Товаров пока нет.</p>
<?php else: ?>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 g-3 mb-4">
        <?php foreach ($products as $product): ?>
            <div class="col">
                <?= $this->render('_product', ['product' => $product]) ?>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($dataProvider->getPagination() !== false && $dataProvider->getPagination()->getPageCount() > 1): ?>
        <?= LinkPager::widget(['pagination' => $dataProvider->getPagination()]) ?>
    <?php endif; ?>
<?php endif; ?>

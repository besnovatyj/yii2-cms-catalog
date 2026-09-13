<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Catalog\entities\product\Product;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\View;

/**
 * Карточка товара в списке.
 *
 * @var View    $this
 * @var Product $product
 */

$url = Url::to(['item', 'id' => $product->id]);
$excerpt = trim((string)($product->description_short !== null && $product->description_short !== ''
    ? $product->description_short
    : strip_tags((string)$product->description)));
?>
<div class="card h-100">
    <?php if ($product->mainPhoto): ?>
        <a href="<?= Html::encode($url) ?>">
            <img src="<?= Html::encode($product->mainPhoto->getThumbUrl('file', 'catalog_list')) ?>"
                 class="card-img-top" alt="<?= Html::encode($product->name) ?>" loading="lazy">
        </a>
    <?php endif; ?>
    <div class="card-body">
        <h2 class="h6 card-title"><?= Html::a(Html::encode($product->name), $url) ?></h2>
        <?php if ($excerpt !== ''): ?>
            <p class="card-text small text-muted"><?= Html::encode(StringHelper::truncateWords($excerpt, 20)) ?></p>
        <?php endif; ?>
    </div>
</div>

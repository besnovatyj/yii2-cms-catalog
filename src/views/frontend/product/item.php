<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Catalog\entities\Category;
use Besnovatyj\Catalog\entities\product\Product;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * Демо-вьюха товара: фото, характеристики, описание, бренд, теги, соседние товары.
 *
 * @var View         $this
 * @var Product      $product
 * @var Product|null $prevProduct
 * @var Product|null $nextProduct
 */

$this->title = $product->getSeoTitle();

$this->registerMetaTag(['name' => 'description', 'content' => $product->meta->description]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $product->meta->keywords]);

$this->params['breadcrumbs'][] = ['label' => 'Каталог', 'url' => ['index']];
if ($product->category !== null) {
    $chain = new TreeQueryScope(Category::class)->parentsQuery($product->category, andSelf: true)->andWhere(['>', 'depth', 0])->all();
    foreach ($chain as $node) {
        $this->params['breadcrumbs'][] = ['label' => $node->name, 'url' => ['by-category', 'slug' => $node->slug]];
    }
    $this->params['active_category'] = $product->category;
}
$this->params['breadcrumbs'][] = $product->name;

$specs = array_filter([
    'Артикул' => $product->code,
    'Характеристика' => $product->spec_primary,
    'Штук' => $product->spec_pieces,
    'Вес' => $product->weight,
    'Количество' => $product->quantity,
], static fn ($value): bool => $value !== null && $value !== '');
?>
<div class="container py-4">
    <div class="row g-4">
        <div class="col-md-6">
            <?php $photos = $product->photos; ?>
            <?php if ($photos !== []): ?>
                <?php foreach ($photos as $i => $photo): ?>
                    <a href="<?= Html::encode($photo->getThumbUrl('file', 'catalog_origin')) ?>" class="d-block mb-2">
                        <img src="<?= Html::encode($photo->getThumbUrl('file', $i === 0 ? 'catalog_product_main' : 'catalog_product_additional')) ?>"
                             class="img-fluid rounded" alt="<?= Html::encode($product->name) ?>" loading="lazy">
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="col-md-6">
            <h1 class="h3"><?= Html::encode($product->name) ?></h1>

            <?php if ($product->brand !== null): ?>
                <p class="mb-2">Бренд:
                    <?= Html::a(Html::encode($product->brand->name), Url::to(['brand', 'slug' => $product->brand->slug])) ?>
                </p>
            <?php endif; ?>

            <?php if ($specs !== []): ?>
                <dl class="row mb-3">
                    <?php foreach ($specs as $label => $value): ?>
                        <dt class="col-sm-4"><?= Html::encode($label) ?></dt>
                        <dd class="col-sm-8"><?= Html::encode((string)$value) ?></dd>
                    <?php endforeach; ?>
                </dl>
            <?php endif; ?>

            <?php if ($product->tags !== []): ?>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                    <span class="text-muted me-1">Теги:</span>
                    <?php foreach ($product->tags as $tag): ?>
                        <?= Html::a(Html::encode($tag->name), Url::to(['/Tags/tag/view', 'slug' => $tag->slug]), ['class' => 'badge text-bg-secondary text-decoration-none']) ?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (trim((string)$product->description) !== ''): ?>
        <div class="mt-4">
            <?= Yii::$app->formatter->asHtml($product->description, [
                'Attr.AllowedRel' => ['nofollow'],
                'HTML.SafeIframe' => true,
                'URI.SafeIframeRegexp' => '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%',
            ]) ?>
        </div>
    <?php endif; ?>

    <?php if ($prevProduct !== null || $nextProduct !== null): ?>
        <nav class="d-flex justify-content-between mt-4" aria-label="Соседние товары">
            <span><?php if ($prevProduct !== null): ?><?= Html::a('← ' . Html::encode($prevProduct->name), Url::to(['item', 'id' => $prevProduct->id])) ?><?php endif; ?></span>
            <span><?php if ($nextProduct !== null): ?><?= Html::a(Html::encode($nextProduct->name) . ' →', Url::to(['item', 'id' => $nextProduct->id])) ?><?php endif; ?></span>
        </nav>
    <?php endif; ?>
</div>

<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Catalog\entities\Category;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;
use yii\data\DataProviderInterface;
use yii\helpers\Html;
use yii\web\View;

/**
 * Демо-вьюха раздела каталога: крошки по дереву, подразделы, описание, товары.
 *
 * @var View                  $this
 * @var Category              $category
 * @var DataProviderInterface $dataProvider товары раздела (ленивый провайдер)
 */

$this->title = $category->getSeoTitle();

$this->registerMetaTag(['name' => 'description', 'content' => $category->meta->description]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $category->meta->keywords]);

$this->params['breadcrumbs'][] = ['label' => 'Каталог', 'url' => ['index']];
// Предки без технического корня дерева (depth 0): у каталога он не показывается (см. getDisplayRoots()).
foreach (new TreeQueryScope(Category::class)->parentsQuery($category)->andWhere(['>', 'depth', 0])->all() as $parent) {
    $this->params['breadcrumbs'][] = ['label' => $parent->name, 'url' => ['by-category', 'slug' => $parent->slug]];
}
$this->params['breadcrumbs'][] = $category->name;

$this->params['active_category'] = $category;
?>
<div class="container py-4">
    <h1 class="h3 mb-3"><?= Html::encode($category->name) ?></h1>

    <?= $this->render('_subcategories', ['category' => $category]) ?>

    <?php if (trim((string)$category->description) !== ''): ?>
        <div class="mb-4">
            <?= Yii::$app->formatter->asHtml($category->description, [
                'Attr.AllowedRel' => ['nofollow'],
                'HTML.SafeIframe' => true,
                'URI.SafeIframeRegexp' => '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%',
            ]) ?>
        </div>
    <?php endif; ?>

    <?= $this->render('_list', ['dataProvider' => $dataProvider]) ?>
</div>

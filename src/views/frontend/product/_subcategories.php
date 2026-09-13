<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Catalog\entities\Category;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

/**
 * Активные подразделы раздела — кнопками. Скрытая ветка ссылкой не предлагается.
 *
 * @var View     $this
 * @var Category $category
 */

$children = new TreeQueryScope(Category::class)->childrenQuery($category)->andWhere(['status' => 1])->all();
?>
<?php if ($children !== []): ?>
    <div class="d-flex flex-wrap gap-2 mb-4">
        <?php foreach ($children as $child): ?>
            <?= Html::a(Html::encode($child->name), Url::to(['by-category', 'slug' => $child->slug]), ['class' => 'btn btn-outline-secondary btn-sm']) ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

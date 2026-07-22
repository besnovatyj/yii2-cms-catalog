<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Catalog\entities\Category;
use Besnovatyj\Catalog\readModels\views\CategoryView;
use yii\helpers\Html;

/**
 * @var CategoryView[] $items
 * @var Category|null $active
 */

?>
<div class="list-group">
    <?php foreach ($items as $view): ?>
        <?php
        $indent = $view->category->depth > 1
            ? str_repeat('&nbsp;&nbsp;&nbsp;', $view->category->depth - 1) . '- '
            : '';
        $isActive = $active && ($active->id == $view->category->id || $active->isChildOf($view->category));
        ?>
        <?= Html::a(
            $indent . Html::encode($view->category->name) . ' (' . $view->count . ')',
            ['/catalog/catalog/category', 'id' => $view->category->id],
            ['class' => $isActive ? 'list-group-item active' : 'list-group-item']
        ) ?>
    <?php endforeach; ?>
</div>

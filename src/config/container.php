<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Catalog\entities\Category;
use Besnovatyj\Catalog\repositories\ProductRepository;
use Besnovatyj\Meta\Meta;
use Besnovatyj\TreeManager\Manager\entities\Node;
use Besnovatyj\TreeManager\Manager\forms\TreeNodeFormInterface;
use Besnovatyj\TreeManager\Manager\TreeManager;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;

/**
 * Конфигурация DI контейнера для модуля Catalog
 */
return function (\yii\di\Container $container): void {
    // TreeManager для таксономий Catalog
    $container->setSingleton('catalog.tree.manager', function () use ($container) {
        $productRepo = new ProductRepository();
        return new TreeManager(
            modelClass: Category::class,
            entityFactory: function (TreeNodeFormInterface $form): Category {
                return Category::create(
                    $form->name,
                    $form->slug,
                    $form->description,
                    new Meta(
                        $form->meta->title,
                        $form->meta->description,
                        $form->meta->keywords,
                    ),
                );
            },
            entityUpdater: function (Node $node, TreeNodeFormInterface $form): Node {
                /** @var Category $node */
                $node->edit(
                    $form->name,
                    $form->slug,
                    $form->description,
                    new Meta(
                        $form->meta->title,
                        $form->meta->description,
                        $form->meta->keywords,
                    ),
                );
                return $node;
            },
            // Опционально: проверка перед удалением
            deleteGuard: function (Node $node) use ($productRepo): void {
                /** @var Category $node */
                if ($productRepo->existsByMainCategory($node->id)) {
                    throw new DomainException('Нельзя удалить категорию с привязанными элементами.');
                }
            },
        );
    });
    // TreeQueryScope для чтения дерева таксономий Catalog
    $container->setSingleton('catalog.tree.scope', function () use ($container) {
        return new TreeQueryScope(Category::class);
    });
};

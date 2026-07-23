<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Catalog\services;

use Besnovatyj\Catalog\entities\showcase\Showcase;
use Besnovatyj\Catalog\entities\showcase\ShowcaseItem;
use Besnovatyj\Catalog\forms\backend\showcase\ShowcaseForm;
use Besnovatyj\Catalog\forms\backend\showcase\ShowcaseItemForm;
use Besnovatyj\Catalog\readModels\ProductReadRepository;
use Besnovatyj\Catalog\repositories\ProductRepository;
use Besnovatyj\Catalog\repositories\ShowcaseRepository;
use DomainException;
use Throwable;
use yii\db\Exception;

/**
 * Сервис управления витринами
 */
class ShowcaseManageService
{
    private ShowcaseRepository $showcases;
    private ProductRepository $products;
    private ProductReadRepository $productsRead;

    public function __construct(
        ShowcaseRepository $showcases,
        ProductRepository  $products,
        ProductReadRepository $productsRead,
    )
    {
        $this->showcases = $showcases;
        $this->products = $products;
        $this->productsRead = $productsRead;
    }

    /**
     * @param ShowcaseForm $form
     * @return Showcase
     * @throws Exception
     */
    public function create(ShowcaseForm $form): Showcase
    {
        $showcase = Showcase::create(
            $form->code,
            $form->name,
            $form->sort,
            $form->getCategoryId(),
        );
        $this->showcases->save($showcase);
        return $showcase;
    }

    /**
     * @param int $id
     * @param ShowcaseForm $form
     * @return void
     * @throws Exception
     */
    public function edit(int $id, ShowcaseForm $form): void
    {
        $showcase = $this->showcases->get($id);
        $showcase->edit($form->code, $form->name, $form->sort, $form->getCategoryId());
        $this->showcases->save($showcase);
    }

    /**
     * @param int $id
     * @return void
     * @throws Exception
     */
    public function activate(int $id): void
    {
        $showcase = $this->showcases->get($id);
        $showcase->activate();
        $this->showcases->save($showcase);
    }

    /**
     * @param int $id
     * @return void
     * @throws Exception
     */
    public function draft(int $id): void
    {
        $showcase = $this->showcases->get($id);
        $showcase->draft();
        $this->showcases->save($showcase);
    }

    /**
     * @param int $id
     * @return void
     * @throws Throwable
     */
    public function remove(int $id): void
    {
        $showcase = $this->showcases->get($id);
        $this->showcases->remove($showcase);
    }

    /**
     * Добавить товар в витрину
     *
     * @param int $showcaseId
     * @param int $productId
     * @return ShowcaseItem
     * @throws Exception
     */
    public function addItem(int $showcaseId, int $productId): ShowcaseItem
    {
        $showcase = $this->showcases->get($showcaseId);
        $this->products->get($productId);

        $maxSort = (int)ShowcaseItem::find()
            ->andWhere(['showcase_id' => $showcase->id])
            ->max('sort');

        $item = ShowcaseItem::create($showcase->id, $productId, $maxSort + 1);
        $this->showcases->saveItem($item);
        return $item;
    }

    /**
     * Дозаполнить витрину товарами привязанной категории.
     *
     * Добавляет как новые элементы только те активные товары категории, которых
     * ещё нет в витрине; порядок и настройки уже существующих элементов не
     * трогает (новые получают sort в конец).
     *
     * @param int $showcaseId
     * @return int сколько товаров добавлено
     * @throws Exception
     */
    public function syncFromCategory(int $showcaseId): int
    {
        $showcase = $this->showcases->get($showcaseId);
        if ($showcase->category_id === null || $showcase->category === null) {
            throw new DomainException('Витрина не привязана к категории.');
        }

        $existingIds = array_map('intval', ShowcaseItem::find()
            ->select('product_id')
            ->andWhere(['showcase_id' => $showcase->id])
            ->column());

        $maxSort = (int)ShowcaseItem::find()
            ->andWhere(['showcase_id' => $showcase->id])
            ->max('sort');

        // Полный (без пагинации) набор товаров категории — тот же, что показывает
        // страница категории в fallback-режиме.
        $provider = $this->productsRead->getAllByCategory($showcase->category);
        $provider->pagination = false;

        $added = 0;
        foreach ($provider->getModels() as $product) {
            if (in_array((int)$product->id, $existingIds, true)) {
                continue;
            }
            $item = ShowcaseItem::create($showcase->id, (int)$product->id, ++$maxSort);
            $this->showcases->saveItem($item);
            $added++;
        }

        return $added;
    }

    /**
     * Настроить элемент витрины
     *
     * @param int $itemId
     * @param ShowcaseItemForm $form
     * @return void
     * @throws Exception
     */
    public function configureItem(int $itemId, ShowcaseItemForm $form): void
    {
        $item = $this->showcases->getItem($itemId);
        $item->configure(
            $form->photo_index !== null && $form->photo_index !== '' ? (int)$form->photo_index : null,
            $form->title_source ?: null,
            $form->description_source ?: null,
        );
        $this->showcases->saveItem($item);
    }

    /**
     * Удалить элемент из витрины
     *
     * @param int $itemId
     * @return void
     * @throws Throwable
     */
    public function removeItem(int $itemId): void
    {
        $item = $this->showcases->getItem($itemId);
        $this->showcases->removeItem($item);
    }

    /**
     * Обновить сортировку элементов витрины
     *
     * @param array $sortData [itemId => newSort, ...]
     * @return void
     * @throws Exception
     */
    public function reorderItems(array $sortData): void
    {
        foreach ($sortData as $itemId => $sort) {
            $item = $this->showcases->getItem((int)$itemId);
            $item->sort = (int)$sort;
            $this->showcases->saveItem($item);
        }
    }

    /**
     * Переключить статус элемента витрины
     *
     * @param int $itemId
     * @return void
     * @throws Exception
     */
    public function toggleItemStatus(int $itemId): void
    {
        $item = $this->showcases->getItem($itemId);
        if ($item->isActive()) {
            $item->status = ShowcaseItem::STATUS_DRAFT;
        } else {
            $item->status = ShowcaseItem::STATUS_ACTIVE;
        }
        $this->showcases->saveItem($item);
    }
}

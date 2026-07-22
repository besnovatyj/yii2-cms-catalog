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
use Besnovatyj\Catalog\repositories\ProductRepository;
use Besnovatyj\Catalog\repositories\ShowcaseRepository;
use Throwable;
use yii\db\Exception;

/**
 * Сервис управления витринами
 */
class ShowcaseManageService
{
    private ShowcaseRepository $showcases;
    private ProductRepository $products;

    public function __construct(
        ShowcaseRepository $showcases,
        ProductRepository  $products,
    )
    {
        $this->showcases = $showcases;
        $this->products = $products;
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
        $showcase->edit($form->code, $form->name, $form->sort);
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
            !empty($form->display_characteristics) ? $form->display_characteristics : null,
            !empty($form->custom_title) ? $form->custom_title : null,
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

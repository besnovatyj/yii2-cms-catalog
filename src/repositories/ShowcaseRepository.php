<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Catalog\repositories;

use Besnovatyj\Catalog\entities\showcase\Showcase;
use Besnovatyj\Catalog\entities\showcase\ShowcaseItem;
use RuntimeException;
use Throwable;
use yii\db\Exception;
use yii\db\StaleObjectException;

/**
 * Репозиторий витрин
 */
class ShowcaseRepository
{
    /**
     * @param int $id
     * @return Showcase
     */
    public function get(int $id): Showcase
    {
        if (!$showcase = Showcase::findOne($id)) {
            throw new NotFoundException('Showcase is not found.');
        }
        return $showcase;
    }

    /**
     * @param string $code
     * @return Showcase
     */
    public function getByCode(string $code): Showcase
    {
        if (!$showcase = Showcase::findOne(['code' => $code])) {
            throw new NotFoundException('Showcase is not found.');
        }
        return $showcase;
    }

    /**
     * @param int $id
     * @return ShowcaseItem
     */
    public function getItem(int $id): ShowcaseItem
    {
        if (!$item = ShowcaseItem::findOne($id)) {
            throw new NotFoundException('Showcase item is not found.');
        }
        return $item;
    }

    /**
     * @param Showcase $showcase
     * @return void
     * @throws Exception
     */
    public function save(Showcase $showcase): void
    {
        if (!$showcase->save()) {
            throw new RuntimeException('Saving error.');
        }
    }

    /**
     * @param ShowcaseItem $item
     * @return void
     * @throws Exception
     */
    public function saveItem(ShowcaseItem $item): void
    {
        if (!$item->save()) {
            throw new RuntimeException('Saving showcase item error.');
        }
    }

    /**
     * @param Showcase $showcase
     * @return void
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function remove(Showcase $showcase): void
    {
        if (!$showcase->delete()) {
            throw new RuntimeException('Removing error.');
        }
    }

    /**
     * @param ShowcaseItem $item
     * @return void
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function removeItem(ShowcaseItem $item): void
    {
        if (!$item->delete()) {
            throw new RuntimeException('Removing showcase item error.');
        }
    }
}

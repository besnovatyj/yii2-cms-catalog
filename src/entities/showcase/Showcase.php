<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Catalog\entities\showcase;

use Besnovatyj\Catalog\entities\Category;
use DomainException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property int $status
 * @property int $sort
 * @property int|null $category_id
 *
 * @property ShowcaseItem[] $items
 * @property Category|null $category
 */
class Showcase extends ActiveRecord
{
    public const int STATUS_DRAFT = 0;
    public const int STATUS_ACTIVE = 1;

    /**
     * @param string $code
     * @param string $name
     * @param int $sort
     * @param int|null $categoryId категория, страницу которой показывает витрина; null = свободная
     * @return self
     */
    public static function create(string $code, string $name, int $sort = 0, ?int $categoryId = null): self
    {
        if (trim($name) === '') {
            throw new DomainException('Showcase name cannot be empty.');
        }
        if (trim($code) === '') {
            throw new DomainException('Showcase code cannot be empty.');
        }

        $showcase = new static();
        $showcase->code = $code;
        $showcase->name = $name;
        $showcase->sort = $sort;
        $showcase->category_id = $categoryId;
        $showcase->status = self::STATUS_DRAFT;
        return $showcase;
    }

    /**
     * @param string $code
     * @param string $name
     * @param int $sort
     * @param int|null $categoryId категория, страницу которой показывает витрина; null = свободная
     * @return void
     */
    public function edit(string $code, string $name, int $sort, ?int $categoryId = null): void
    {
        if (trim($name) === '') {
            throw new DomainException('Showcase name cannot be empty.');
        }
        if (trim($code) === '') {
            throw new DomainException('Showcase code cannot be empty.');
        }

        $this->code = $code;
        $this->name = $name;
        $this->sort = $sort;
        $this->category_id = $categoryId;
    }

    /**
     * @return void
     */
    public function activate(): void
    {
        if ($this->isActive()) {
            throw new DomainException('Showcase is already active.');
        }
        $this->status = self::STATUS_ACTIVE;
    }

    /**
     * @return void
     */
    public function draft(): void
    {
        if ($this->isDraft()) {
            throw new DomainException('Showcase is already draft.');
        }
        $this->status = self::STATUS_DRAFT;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    /**
     * @return bool
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    // <editor-fold desc="Relations">

    /**
     * @return ActiveQuery
     */
    public function getItems(): ActiveQuery
    {
        return $this->hasMany(ShowcaseItem::class, ['showcase_id' => 'id'])->orderBy(['sort' => SORT_ASC]);
    }

    /**
     * Категория, к странице которой привязана витрина (null = свободная витрина).
     *
     * @return ActiveQuery
     */
    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    // </editor-fold>

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%catalog_showcases}}';
    }
}

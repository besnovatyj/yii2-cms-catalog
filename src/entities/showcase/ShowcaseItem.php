<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Catalog\entities\showcase;

use Besnovatyj\Catalog\entities\product\Photo;
use Besnovatyj\Catalog\entities\product\Product;
use DomainException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $showcase_id
 * @property int $product_id
 * @property int|null $photo_index
 * @property string|null $display_characteristics
 * @property string|null $custom_title
 * @property int $sort
 * @property int $status
 *
 * @property Showcase $showcase
 * @property Product $product
 */
class ShowcaseItem extends ActiveRecord
{
    public const int STATUS_DRAFT = 0;
    public const int STATUS_ACTIVE = 1;

    /**
     * @param int $showcaseId
     * @param int $productId
     * @param int $sort
     * @return self
     */
    public static function create(int $showcaseId, int $productId, int $sort = 0): self
    {
        $item = new static();
        $item->showcase_id = $showcaseId;
        $item->product_id = $productId;
        $item->sort = $sort;
        $item->status = self::STATUS_ACTIVE;
        return $item;
    }

    /**
     * @param int|null $photoIndex
     * @param array|null $displayCharacteristics
     * @param string|null $customTitle
     * @return void
     */
    public function configure(?int $photoIndex, ?array $displayCharacteristics, ?string $customTitle): void
    {
        $this->photo_index = $photoIndex;
        $this->display_characteristics = $displayCharacteristics !== null
            ? json_encode($displayCharacteristics, JSON_UNESCAPED_UNICODE)
            : null;
        $this->custom_title = $customTitle;
    }

    /**
     * Возвращает фото для отображения в витрине
     *
     * Приоритет:
     * 1. Конкретное фото по photo_index
     * 2. Главное фото товара (fallback)
     *
     * @return Photo|null
     */
    public function getDisplayPhoto(): ?Photo
    {
        if ($this->photo_index !== null) {
            $photos = $this->product->photos;
            return $photos[$this->photo_index] ?? $this->product->mainPhoto;
        }
        return $this->product->mainPhoto;
    }

    /**
     * Возвращает заголовок для отображения в витрине
     *
     * @return string
     */
    public function getDisplayTitle(): string
    {
        return $this->custom_title ?: ($this->product->name_short ?: $this->product->name);
    }

    /**
     * Возвращает массив slug-ов характеристик для отображения
     *
     * @return array
     */
    public function getDisplayCharacteristicSlugs(): array
    {
        if ($this->display_characteristics === null) {
            return [];
        }
        $decoded = json_decode($this->display_characteristics, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @return void
     */
    public function activate(): void
    {
        if ($this->isActive()) {
            throw new DomainException('Showcase item is already active.');
        }
        $this->status = self::STATUS_ACTIVE;
    }

    /**
     * @return void
     */
    public function draft(): void
    {
        if ($this->isDraft()) {
            throw new DomainException('Showcase item is already draft.');
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
    public function getShowcase(): ActiveQuery
    {
        return $this->hasOne(Showcase::class, ['id' => 'showcase_id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getProduct(): ActiveQuery
    {
        return $this->hasOne(Product::class, ['id' => 'product_id']);
    }

    // </editor-fold>

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%catalog_showcase_items}}';
    }
}

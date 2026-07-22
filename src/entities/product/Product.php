<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\entities\product;

use Besnovatyj\Catalog\entities\Brand;
use Besnovatyj\Catalog\entities\Category;
use Besnovatyj\Catalog\entities\product\queries\ProductQuery;
use Besnovatyj\Catalog\entities\Tag;
use Besnovatyj\DomainEvents\AggregateRoot;
use Besnovatyj\DomainEvents\EventTrait;
use Besnovatyj\Helpers\FilesystemHelper;
use Besnovatyj\Meta\Meta;
use Besnovatyj\Meta\MetaBehavior;
use Besnovatyj\PessimisticLock\PessimisticLockBehavior;
use DateTimeImmutable;
use DomainException;
use Exception;
use Throwable;
use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $created_at
 * @property string $code
 * @property string $name
 * @property string $name_short
 * @property string $description
 * @property string $description_short
 * @property int $category_id
 * @property int $brand_id
 * @property int $rating
 * @property int $main_photo_id
 * @property int $status
 * @property string $weight
 * @property int $quantity
 *
 * @property Meta $meta
 * @property Brand $brand
 * @property Category $category
 * @property CategoryAssignment[] $categoryAssignments
 * @property Category[] $categories
 * @property TagAssignment[] $tagAssignments
 * @property Tag[] $tags
 * @property RelatedAssignment[] $relatedAssignments
 * @property Value[] $values
 * @property Photo[] $photos
 * @property Photo $mainPhoto
 *
 * @mixin PessimisticLockBehavior
 */
class Product extends ActiveRecord implements AggregateRoot
{
    use EventTrait;

    public const int STATUS_DRAFT = 0;
    public const int STATUS_ACTIVE = 1;

    public Meta $meta;

    public static function create(int $brandId, int $categoryId, ?string $code, string $name, ?string $name_short, ?string $description, ?string $description_short, ?string $weight, Meta $meta): self
    {
        if (trim($name) === '') {
            throw new DomainException('Product name cannot be empty.');
        }

        $product = new static();
        $product->brand_id = $brandId;
        $product->category_id = $categoryId;
        $product->code = $code ?: uniqid('', true);
        $product->name = $name;
        $product->name_short = $name_short;
        $product->description = $description;
        $product->description_short = $description_short;
        $product->weight = $weight;
        $product->meta = $meta;
        $product->status = self::STATUS_DRAFT;
        $product->created_at = new DateTimeImmutable()->format('Y.m.d H:i:s');
        return $product;
    }

    public function edit(int $brandId, ?string $code, string $name, ?string $name_short, ?string $description, ?string $description_short, ?string $weight, Meta $meta): void
    {
        if (trim($name) === '') {
            throw new DomainException('Product name cannot be empty.');
        }

        $this->brand_id = $brandId;
        $this->code = $code ?: uniqid('', true);
        $this->name = $name;
        $this->name_short = $name_short;
        $this->description = $description;
        $this->description_short = $description_short;
        $this->weight = $weight;
        $this->meta = $meta;
    }

    public function changeMainCategory(int $categoryId): void
    {
        $this->category_id = $categoryId;
    }

    public function activate(): void
    {
        if ($this->isActive()) {
            throw new DomainException('Product is already active.');
        }
        $this->status = self::STATUS_ACTIVE;
    }

    public function draft(): void
    {
        if ($this->isDraft()) {
            throw new DomainException('Product is already draft.');
        }
        $this->status = self::STATUS_DRAFT;
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function getSeoTitle(): string
    {
        return $this->meta->title ?: $this->name;
    }

    public function getValue(int $characteristic_id): Value
    {
        $values = $this->values;
        foreach ($values as $val) {
            if ($val->isForCharacteristic($characteristic_id)) {
                return $val;
            }
        }
        return Value::blank($characteristic_id);
    }

    // <editor-fold desc="Images">

    public function setMainPhoto(?int $imageId): void
    {
        $this->main_photo_id = $imageId;
    }

    // </editor-fold>

    // <editor-fold desc="Relations">

    public function getBrand(): ActiveQuery
    {
        return $this->hasOne(Brand::class, ['id' => 'brand_id']);
    }

    public function getCategory(): ActiveQuery
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    public function getCategoryAssignments(): ActiveQuery
    {
        return $this->hasMany(CategoryAssignment::class, ['product_id' => 'id']);
    }

    public function getCategories(): ActiveQuery
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])->via('categoryAssignments');
    }

    public function getTagAssignments(): ActiveQuery
    {
        return $this->hasMany(TagAssignment::class, ['product_id' => 'id']);
    }

    public function getTags(): ActiveQuery
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])->via('tagAssignments');
    }

    public function getValues(): ActiveQuery
    {
        return $this->hasMany(Value::class, ['product_id' => 'id']);
    }

    public function getPhotos(): ActiveQuery
    {
        return $this->hasMany(Photo::class, ['product_id' => 'id'])->orderBy('sort');
    }

    public function getMainPhoto(): ActiveQuery
    {
        return $this->hasOne(Photo::class, ['id' => 'main_photo_id']);
    }

    public function getRelatedAssignments(): ActiveQuery
    {
        return $this->hasMany(RelatedAssignment::class, ['product_id' => 'id']);
    }

    public function getRelates(): ActiveQuery
    {
        return $this->hasMany(__CLASS__, ['id' => 'related_id'])->via('relatedAssignments');
    }

    // </editor-fold>

    // <editor-fold desc="Events">

    /**
     * @throws \yii\base\Exception
     */
    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);
        FilesystemHelper::createDirectoryRecursively(Yii::getAlias('@static/origin/Catalog/Products/' . $this->id));
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    public function beforeDelete(): bool
    {
        if (parent::beforeDelete()) {
            if ($this->photos) {
                foreach ($this->photos as $photo) {
                    $photo->delete();
                }
            }

            $origin = Yii::getAlias('@static/origin/Catalog/Products/') . '/' . $this->id;
            $cache = Yii::getAlias('@static/cache/Catalog/Products/') . '/' . $this->id;
            FilesystemHelper::deleteDirContents($origin, true);
            FilesystemHelper::deleteDirContents($cache, true);

            return true;
        }
        return false;
    }

    // </editor-fold>

    public static function tableName(): string
    {
        return '{{%catalog_products}}';
    }

    public function behaviors(): array
    {
        return [
            MetaBehavior::class,
            PessimisticLockBehavior::class,
            ...parent::behaviors()
        ];
    }

    public function transactions(): array
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find(): ProductQuery
    {
        return new ProductQuery(static::class);
    }
}

<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\entities;

use Besnovatyj\Catalog\entities\queries\CategoryQuery;
use Besnovatyj\Helpers\FilesystemHelper;
use Besnovatyj\Meta\Meta;
use Besnovatyj\Meta\MetaBehavior;
use Besnovatyj\TreeManager\Manager\entities\Node;
use DomainException;
use Exception;
use Yii;

/**
 * @property integer $id
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property integer $tree
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property integer $status
 * @property int $sort_order - Порядок сортировки корневых узлов
 *
 * @property Meta $meta
 *
 * @mixin MetaBehavior
 */
class Category extends Node
{
    public Meta|null $meta = null;

    public static function create(string $name, string $slug, ?string $description, Meta $meta): self
    {
        if (trim($name) === '') {
            throw new DomainException('Category name cannot be empty.');
        }
        if (trim($slug) === '') {
            throw new DomainException('Category slug cannot be empty.');
        }

        $category = new static();
        $category->name = $name;
        $category->slug = $slug;
        $category->description = $description;
        $category->meta = $meta;
        return $category;
    }

    public function edit(string $name, string $slug, ?string $description, Meta $meta): void
    {
        if (trim($name) === '') {
            throw new DomainException('Category name cannot be empty.');
        }
        if (trim($slug) === '') {
            throw new DomainException('Category slug cannot be empty.');
        }

        $this->name = $name;
        $this->slug = $slug;
        $this->description = $description;
        $this->meta = $meta;
    }

    public function getSeoTitle(): string
    {
        return $this->meta->title ?: $this->name;
    }

    public function changeStatus(): void
    {
        $this->status = !$this->status;
    }

    public static function tableName(): string
    {
        return '{{%catalog_categories}}';
    }

    /**
     * @throws \yii\base\Exception
     */
    public function afterSave($insert, $changedAttributes): void
    {
        parent::afterSave($insert, $changedAttributes);
        FilesystemHelper::createDirectoryRecursively(\Yii::getAlias('@static/origin/Catalog/Categories/' . $this->id));
    }

    /**
     * @throws Exception
     */
    public function beforeDelete(): bool
    {
        if (parent::beforeDelete()) {
            $origin = Yii::getAlias('@static/origin/Catalog/Categories/') . '/' . $this->id;
            $cache = Yii::getAlias('@static/cache/Catalog/Categories/') . '/' . $this->id;
            FilesystemHelper::deleteDirContents($origin, true);
            FilesystemHelper::deleteDirContents($cache, true);
            return true;
        }
        return false;
    }

    public function behaviors(): array
    {
        return [
            MetaBehavior::class,
            ...parent::behaviors()
        ];
    }

    public function transactions(): array
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find(): CategoryQuery
    {
        return new CategoryQuery(static::class);
    }
}

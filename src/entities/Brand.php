<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\entities;

use Besnovatyj\Meta\MetaBehavior;
use Besnovatyj\Meta\Meta;
use DomainException;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property Meta $meta
 */
class Brand extends ActiveRecord
{
    public $meta;

    public static function create(string $name, string $slug, Meta $meta): self
    {
        if (trim($name) === '') {
            throw new DomainException('Brand name cannot be empty.');
        }
        if (trim($slug) === '') {
            throw new DomainException('Brand slug cannot be empty.');
        }

        $brand = new static();
        $brand->name = $name;
        $brand->slug = $slug;
        $brand->meta = $meta;
        return $brand;
    }

    public function edit(string $name, string $slug, Meta $meta): void
    {
        if (trim($name) === '') {
            throw new DomainException('Brand name cannot be empty.');
        }
        if (trim($slug) === '') {
            throw new DomainException('Brand slug cannot be empty.');
        }

        $this->name = $name;
        $this->slug = $slug;
        $this->meta = $meta;
    }

    public function getSeoTitle(): string
    {
        return $this->meta->title ?: $this->name;
    }

    ##########################

    public static function tableName(): string
    {
        return '{{%catalog_brands}}';
    }

    public function behaviors(): array
    {
        return [
            MetaBehavior::class,
        ];
    }
}

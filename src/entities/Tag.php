<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\entities;

use DomainException;
use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 */
class Tag extends ActiveRecord
{
    public static function create(string $name, string $slug): self
    {
        if (trim($name) === '') {
            throw new DomainException('Tag name cannot be empty.');
        }
        if (trim($slug) === '') {
            throw new DomainException('Tag slug cannot be empty.');
        }

        $tag = new static();
        $tag->name = $name;
        $tag->slug = $slug;
        return $tag;
    }

    public function edit(string $name, string $slug): void
    {
        if (trim($name) === '') {
            throw new DomainException('Tag name cannot be empty.');
        }
        if (trim($slug) === '') {
            throw new DomainException('Tag slug cannot be empty.');
        }

        $this->name = $name;
        $this->slug = $slug;
    }

    public static function tableName(): string
    {
        return '{{%catalog_tags}}';
    }
}

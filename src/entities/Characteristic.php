<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\entities;

use DomainException;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * @property integer $id
 * @property string $name
 * @property string $slug
 * @property string $type
 * @property integer $required
 * @property string $default
 * @property array $variants
 * @property integer $sort
 */
class Characteristic extends ActiveRecord
{
    public const string TYPE_STRING = 'string';
    public const string TYPE_INTEGER = 'integer';
    public const string TYPE_FLOAT = 'float';

    public array $variants = [];

    public static function create(string $name, string $slug, string $type, int $required, ?string $default, array $variants, int $sort): self
    {
        if (trim($name) === '') {
            throw new DomainException('Characteristic name cannot be empty.');
        }
        if (trim($slug) === '') {
            throw new DomainException('Characteristic slug cannot be empty.');
        }
        if (!in_array($type, [self::TYPE_STRING, self::TYPE_INTEGER, self::TYPE_FLOAT], true)) {
            throw new DomainException("Invalid characteristic type: $type.");
        }

        $object = new static();
        $object->name = $name;
        $object->slug = $slug;
        $object->type = $type;
        $object->required = $required;
        $object->default = $default;
        $object->variants = $variants;
        $object->sort = $sort;
        return $object;
    }

    public function edit(string $name, string $slug, string $type, int $required, ?string $default, array $variants, int $sort): void
    {
        if (trim($name) === '') {
            throw new DomainException('Characteristic name cannot be empty.');
        }
        if (trim($slug) === '') {
            throw new DomainException('Characteristic slug cannot be empty.');
        }
        if (!in_array($type, [self::TYPE_STRING, self::TYPE_INTEGER, self::TYPE_FLOAT], true)) {
            throw new DomainException("Invalid characteristic type: $type.");
        }

        $this->name = $name;
        $this->slug = $slug;
        $this->type = $type;
        $this->required = $required;
        $this->default = $default;
        $this->variants = $variants;
        $this->sort = $sort;
    }

    public function isString(): bool
    {
        return $this->type === self::TYPE_STRING;
    }

    public function isInteger(): bool
    {
        return $this->type === self::TYPE_INTEGER;
    }

    public function isFloat(): bool
    {
        return $this->type === self::TYPE_FLOAT;
    }

    public function isSelect(): bool
    {
        return count($this->variants) > 0;
    }

    public static function tableName(): string
    {
        return '{{%catalog_characteristics}}';
    }

    public function afterFind(): void
    {
        $this->variants = array_filter(Json::decode($this->getAttribute('variants_json')));
        parent::afterFind();
    }

    public function beforeSave($insert): bool
    {
        $this->setAttribute('variants_json', Json::encode(array_filter($this->variants)));
        return parent::beforeSave($insert);
    }
}

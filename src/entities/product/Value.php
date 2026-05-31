<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\entities\product;

use Besnovatyj\Catalog\entities\Characteristic;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property integer $product_id
 * @property integer $characteristic_id
 * @property string $value
 * @property string $slug
 *
 * @property Characteristic $characteristic
 */
class Value extends ActiveRecord
{
    public static function create(int $characteristicId, string $value, string $slug): self
    {
        $valueObject = new static();
        $valueObject->characteristic_id = $characteristicId;
        $valueObject->value = $value;
        $valueObject->slug = $slug;
        return $valueObject;
    }

    /**
     * Если при редактировании продукта у него не заполнена какая-либо характеристика, отдаём пустую для создания формы
     * Непонятно только почему надо отдавать пустую сущность
     */
    public static function blank(int $characteristicId): self
    {
        $valueObject = new static();
        $valueObject->characteristic_id = $characteristicId;
        return $valueObject;
    }

    public function change($value): void
    {
        $this->value = $value;
    }

    public function isForCharacteristic(int $id): bool
    {
        return $this->characteristic_id === $id;
    }

    public function getCharacteristic(): ActiveQuery
    {
        return $this->hasOne(Characteristic::class, ['id' => 'characteristic_id']);
    }

    public static function tableName(): string
    {
        return '{{%catalog_values}}';
    }
}

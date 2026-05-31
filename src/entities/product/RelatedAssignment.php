<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\entities\product;

use yii\db\ActiveRecord;

/**
 * @property int $product_id;
 * @property int $related_id;
 */
class RelatedAssignment extends ActiveRecord
{
    public static function create(int $productId): self
    {
        $assignment = new static();
        $assignment->related_id = $productId;
        return $assignment;
    }

    public function isForProduct(int $id): bool
    {
        return $this->related_id == $id;
    }

    public static function tableName(): string
    {
        return '{{%catalog_related_assignments}}';
    }
}

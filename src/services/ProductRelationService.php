<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Catalog\services;

use Besnovatyj\Catalog\entities\product\RelatedAssignment;
use Besnovatyj\Catalog\repositories\ProductRepository;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 * Управление связанными продуктами
 */
class ProductRelationService
{
    private ProductRepository $products;

    public function __construct(ProductRepository $products)
    {
        $this->products = $products;
    }

    /**
     * @throws Exception
     */
    public function add(int $id, int $otherId): void
    {
        $product = $this->products->get($id);

        $existingIds = ArrayHelper::getColumn($product->relatedAssignments, 'related_id');
        if (in_array($otherId, $existingIds, true)) {
            return;
        }

        $this->products->get($otherId);  // Проверка существования сущности

        $assignment = new RelatedAssignment();
        $assignment->product_id = $product->id;
        $assignment->related_id = $otherId;

        if (!$assignment->save()) {
            throw new Exception('Failed to save product related assignment.');
        }
    }

    public function remove(int $id, int $otherId): void
    {
        $product = $this->products->get($id);
        $existingIds = ArrayHelper::getColumn($product->relatedAssignments, 'related_id');
        if (!in_array($otherId, $existingIds, true)) {
            return;
        }
        // Проверка существования сущности
        $this->products->get($otherId);
        RelatedAssignment::deleteAll(['product_id' => $product->id, 'related_id' => $otherId]);
    }
}

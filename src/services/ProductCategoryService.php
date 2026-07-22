<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Catalog\services;

use Besnovatyj\Catalog\entities\product\CategoryAssignment;
use Besnovatyj\Catalog\entities\product\Product;
use Besnovatyj\Catalog\repositories\CategoryRepository;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 * Управление привязкой категорий к продукту
 */
class ProductCategoryService
{
    private CategoryRepository $categories;

    public function __construct(CategoryRepository $categories)
    {
        $this->categories = $categories;
    }

    /**
     * @param int[] $categoryIds
     * @throws Exception
     */
    public function assign(Product $product, array $categoryIds): void
    {
        $existingIds = ArrayHelper::getColumn($product->categoryAssignments, 'category_id');

        foreach ($categoryIds as $categoryId) {
            if (in_array($categoryId, $existingIds, true)) {
                continue;
            }

            // Проверка существования сущности
            $this->categories->get($categoryId);

            $assignment = new CategoryAssignment();
            $assignment->product_id = $product->id;
            $assignment->category_id = $categoryId;

            if (!$assignment->save()) {
                throw new Exception('Failed to save category assignment.');
            }
        }
    }

    public function revoke(Product $product): void
    {
        CategoryAssignment::deleteAll(['product_id' => $product->id]);
    }
}

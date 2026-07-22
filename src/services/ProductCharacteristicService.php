<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Catalog\services;

use Besnovatyj\Catalog\entities\product\Product;
use Besnovatyj\Catalog\entities\product\Value;
use Besnovatyj\Catalog\forms\backend\product\ValueForm;
use yii\db\Exception;

/**
 * Управление привязкой характеристик к продукту
 */
class ProductCharacteristicService
{
    /**
     * @param Product $product
     * @param ValueForm[] $valueForms
     * @throws Exception
     */
    public function assign(Product $product, array $valueForms): void
    {
        foreach ($valueForms as $valueForm) {
            if ($valueForm->value === null || $valueForm->value === '') {
                continue;
            }

            $value = new Value();
            $value->product_id = $product->id;
            $value->characteristic_id = $valueForm->getCharacteristicId();
            $value->value = $valueForm->value;
            $value->slug = $valueForm->getCharacteristicSlug();

            if (!$value->save()) {
                throw new Exception('Failed to save characteristic value.');
            }
        }
    }

    public function revoke(Product $product): void
    {
        Value::deleteAll(['product_id' => $product->id]);
    }
}

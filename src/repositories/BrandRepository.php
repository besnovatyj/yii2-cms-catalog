<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\repositories;

use Besnovatyj\Catalog\entities\Brand;
use RuntimeException;
use Throwable;
use yii\db\Exception;
use yii\db\StaleObjectException;

class BrandRepository
{
    public function get($id): Brand
    {
        if (!$brand = Brand::findOne($id)) {
            throw new NotFoundException('Brand is not found.');
        }
        return $brand;
    }

    /**
     * @throws Exception
     */
    public function save(Brand $brand): void
    {
        if (!$brand->save()) {
            throw new RuntimeException('Saving error.');
        }
    }

    /**
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function remove(Brand $brand): void
    {
        if (!$brand->delete()) {
            throw new RuntimeException('Removing error.');
        }
    }
}

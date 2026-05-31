<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\readModels;

use Besnovatyj\Catalog\entities\Brand;

class BrandReadRepository
{
    public function find($id): ?Brand
    {
        return Brand::findOne($id);
    }
}

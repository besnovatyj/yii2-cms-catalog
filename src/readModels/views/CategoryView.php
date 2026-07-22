<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\readModels\views;

use Besnovatyj\Catalog\entities\Category;

class CategoryView
{
    public Category $category;
    public int $count;

    public function __construct(Category $category, int $count)
    {
        $this->category = $category;
        $this->count = $count;
    }
}

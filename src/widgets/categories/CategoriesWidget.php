<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace categories;

use Besnovatyj\Catalog\entities\Category;
use Besnovatyj\Catalog\readModels\CategoryReadRepository;
use yii\base\Widget;

class CategoriesWidget extends Widget
{
    /** @var Category|null */
    public ?Category $active;

    private CategoryReadRepository $categories;

    public function __construct(CategoryReadRepository $categories, $config = [])
    {
        parent::__construct($config);
        $this->categories = $categories;
    }

    public function run(): string
    {
        return $this->render('categories', [
            'items' => $this->categories->getTreeWithSubsOf($this->active),
            'active' => $this->active,
        ]);
    }
}

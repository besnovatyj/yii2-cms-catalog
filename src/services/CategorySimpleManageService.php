<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\services;

use Besnovatyj\Catalog\repositories\CategoryRepository;
use Besnovatyj\Meta\Meta;
use Besnovatyj\Catalog\forms\backend\CategoryForm;
use yii\db\Exception;

class CategorySimpleManageService
{
    private CategoryRepository $categories;

    public function __construct(CategoryRepository $categories)
    {
        $this->categories = $categories;
    }

    /**
     * @throws Exception
     */
    public function edit($id, CategoryForm $form): void
    {
        $category = $this->categories->get($id);
        $category->edit(
            $form->name,
            $form->slug,
            $form->description,
            new Meta(
                $form->meta->title,
                $form->meta->description,
                $form->meta->keywords
            )
        );
        $this->categories->save($category);
    }

}

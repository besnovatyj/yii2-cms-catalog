<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\controllers\backend;

use Besnovatyj\Catalog\entities\Category;
use Besnovatyj\Catalog\forms\backend\CategoryForm;
use Besnovatyj\TreeManager\Manager\controllers\TreeController;
use Besnovatyj\TreeManager\Manager\TreeDataSource;
use Yii;

class CategoryController extends TreeController
{
    public function __construct($id, $module, $config = [])
    {
        $this->treeManager = Yii::$container->get('catalog.tree.manager');
        $this->dataSource = new TreeDataSource(
            Category::class,
            function (Category $model) {
                return [
                    'id' => $model->id,
                    'title' => $model->name,
                    'slug' => $model->slug,
                ];
            },
            'sort_order' // Атрибут сортировки корней
        );

        $this->createFormClass = CategoryForm::class;
        $this->updateFormClass = CategoryForm::class;
        $this->formView = '_form';
        $this->indexTitle = 'Управление категориями';

        parent::__construct($id, $module, $config);
    }
}

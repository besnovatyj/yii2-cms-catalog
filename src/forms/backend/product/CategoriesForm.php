<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\forms\backend\product;

use Besnovatyj\Catalog\entities\Category;
use Besnovatyj\Catalog\entities\product\Product;
use Besnovatyj\Forms\BaseForm;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;
use yii\helpers\ArrayHelper;

class CategoriesForm extends BaseForm
{
    public int|null $main = null;
    public array|string $others = []; // TODO - Если не заполнять, то из html формы прилетает пустая строка. А так вообще только массив

    public function __construct(?Product $product = null, $config = [])
    {
        if ($product) {
            $this->main = $product->category_id;
            $this->others = ArrayHelper::getColumn($product->categoryAssignments, 'category_id');
        }
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['main', 'required'],
            ['main', 'integer'],
            ['others', 'each', 'rule' => ['integer']],
            ['others', 'default', 'value' => []],
        ];
    }

    public function categoriesList(): array
    {
        $scope = new TreeQueryScope(Category::class);
        return $scope->dropdownTree();
    }

}

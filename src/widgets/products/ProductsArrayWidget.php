<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\widgets\products;

use Besnovatyj\Catalog\readModels\ProductReadRepository;
use yii\base\Widget;

class ProductsArrayWidget extends Widget
{
    public ProductReadRepository $repository;
    public array $products = [];

    public function __construct(ProductReadRepository $repository, $config = [])
    {
        parent::__construct($config);
        $this->repository = $repository;
        $dataProvider = $this->repository->getAll();
        $products = $dataProvider->getModels();

        if (!empty($products)) {
            $this->products = $products;
        }
    }

}

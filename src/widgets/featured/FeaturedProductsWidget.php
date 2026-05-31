<?php



/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace featured;

use Besnovatyj\Catalog\readModels\ProductReadRepository;
use yii\base\Widget;

class FeaturedProductsWidget extends Widget
{
    public int $limit = 10;

    private ProductReadRepository $repository;

    public function __construct(ProductReadRepository $repository, $config = [])
    {
        parent::__construct($config);
        $this->repository = $repository;
    }

    public function run()
    {
        return $this->render('featured', [
            'products' => $this->repository->getFeatured($this->limit)
        ]);
    }
}

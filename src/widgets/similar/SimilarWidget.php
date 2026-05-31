<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\widgets\similar;

use Besnovatyj\Catalog\entities\product\Product;
use Besnovatyj\Catalog\readModels\ProductReadRepository;
use yii\base\Widget;

/**
 * Виджет принимает идентификатор товара и возвращает все товары из категории переданного,
 * за исключением самого переданного.
 */
class SimilarWidget extends Widget
{
    public int $productId = 0;
    public array $products = [];
    private ProductReadRepository $repository;

    public function __construct(ProductReadRepository $repository, $config = [])
    {
        parent::__construct($config);
        $this->repository = $repository;
        $this->products = $this->getProducts();
    }

    private function getProducts(): array
    {
        if ($this->productId > 0) {
            $receivedProduct = $this->repository->find($this->productId);

            if ($receivedProduct instanceof Product) {

                $category = $receivedProduct->getCategory()->one();
                $similarProductsDataProvider = $this->repository->getAllByCategory($category);
                $similarProducts = $similarProductsDataProvider->getModels();

                $filteredSimilarProducts = array_filter($similarProducts, function (Product $product) {
                    return $product->id !== $this->productId;
                });

                if (count($similarProducts) > 0) {
                    return $filteredSimilarProducts;
                }

            }
        }

        return [];
    }

}

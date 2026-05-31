<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Catalog\services;

use Besnovatyj\Catalog\entities\product\Product;
use Besnovatyj\Catalog\forms\backend\product\ProductForm;
use Besnovatyj\Catalog\repositories\BrandRepository;
use Besnovatyj\Catalog\repositories\CategoryRepository;
use Besnovatyj\Catalog\repositories\ProductRepository;
use Besnovatyj\DomainEvents\TransactionManager;
use Besnovatyj\Meta\Meta;
use Throwable;
use yii\db\Exception;
use yii\db\StaleObjectException;

/**
 * Оркестратор управления продуктами
 */
class ProductManageService
{
    private ProductRepository $products;
    private BrandRepository $brands;
    private CategoryRepository $categories;
    private ProductCategoryService $categoryService;
    private ProductTagService $tagService;
    private ProductCharacteristicService $characteristicService;
    private TransactionManager $transaction;

    public function __construct(
        ProductRepository            $products,
        BrandRepository              $brands,
        CategoryRepository           $categories,
        ProductCategoryService       $categoryService,
        ProductTagService            $tagService,
        ProductCharacteristicService $characteristicService,
        TransactionManager           $transaction
    )
    {
        $this->products = $products;
        $this->brands = $brands;
        $this->categories = $categories;
        $this->categoryService = $categoryService;
        $this->tagService = $tagService;
        $this->characteristicService = $characteristicService;
        $this->transaction = $transaction;
    }

    /**
     * @throws Throwable
     */
    public function create(ProductForm $form): Product
    {
        $brand = $this->brands->get($form->brandId);
        $category = $this->categories->get($form->categoriesForm->main);

        $product = Product::create(
            $brand->id,
            $category->id,
            $form->code,
            $form->name,
            $form->name_short,
            $form->description,
            $form->description_short,
            $form->weight,
            new Meta(
                $form->metaForm->title,
                $form->metaForm->description,
                $form->metaForm->keywords
            )
        );

        $this->transaction->wrap(function () use ($product, $form) {
            $this->products->save($product);

            $this->categoryService->assign($product, $form->categoriesForm->others);
            $this->tagService->assign($product, $form->tagsForm->newTagsNames);
            $this->characteristicService->assign($product, $form->valueForms);

        });
        return $product;
    }

    /**
     * @throws Throwable
     */
    public function edit(Product $product, ProductForm $form): void
    {
        $brand = $this->brands->get($form->brandId);
        $category = $this->categories->get($form->categoriesForm->main);

        $product->edit(
            $brand->id,
            $form->code,
            $form->name,
            $form->name_short,
            $form->description,
            $form->description_short,
            $form->weight,
            new Meta(
                $form->metaForm->title,
                $form->metaForm->description,
                $form->metaForm->keywords
            )
        );

        $product->changeMainCategory($category->id);

        $this->transaction->wrap(function () use ($product, $form) {

            $this->products->save($product);

            $this->categoryService->revoke($product);
            $this->tagService->revoke($product);
            $this->characteristicService->revoke($product);

            $this->categoryService->assign($product, $form->categoriesForm->others);
            $this->tagService->assign($product, $form->tagsForm->newTagsNames);
            $this->characteristicService->assign($product, $form->valueForms);

        });
    }

    /**
     * @throws Exception
     */
    public function activate($id): void
    {
        $product = $this->products->get($id);
        $product->activate();
        $this->products->save($product);
    }

    /**
     * @throws Exception
     */
    public function draft($id): void
    {
        $product = $this->products->get($id);
        $product->draft();
        $this->products->save($product);
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function remove(int $id): void
    {
        $product = $this->products->get($id);

        $this->transaction->wrap(function () use ($product) {
            $this->categoryService->revoke($product);
            $this->tagService->revoke($product);
            $this->characteristicService->revoke($product);
            $this->products->remove($product);
        });
    }
}

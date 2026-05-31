<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\services;

use Besnovatyj\Catalog\repositories\BrandRepository;
use Besnovatyj\Catalog\repositories\ProductRepository;
use Besnovatyj\Meta\Meta;
use Besnovatyj\Catalog\entities\Brand;
use Besnovatyj\Catalog\forms\backend\BrandForm;
use DomainException;
use Throwable;
use yii\db\Exception;
use yii\db\StaleObjectException;

class BrandManageService
{
    private BrandRepository $brands;
    private ProductRepository $products;

    public function __construct(BrandRepository $brands, ProductRepository $products)
    {
        $this->brands = $brands;
        $this->products = $products;
    }

    /**
     * @throws Exception
     */
    public function create(BrandForm $form): Brand
    {
        $brand = Brand::create(
            $form->name,
            $form->slug,
            new Meta(
                $form->meta->title,
                $form->meta->description,
                $form->meta->keywords
            )
        );
        $this->brands->save($brand);
        return $brand;
    }

    /**
     * @throws Exception
     */
    public function edit($id, BrandForm $form): void
    {
        $brand = $this->brands->get($id);
        $brand->edit(
            $form->name,
            $form->slug,
            new Meta(
                $form->meta->title,
                $form->meta->description,
                $form->meta->keywords
            )
        );
        $this->brands->save($brand);
    }

    /**
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function remove($id): void
    {
        $brand = $this->brands->get($id);
        if ($this->products->existsByBrand($brand->id)) {
            throw new DomainException('Unable to remove brand with products.');
        }
        $this->brands->remove($brand);
    }
}

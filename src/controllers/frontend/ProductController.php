<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\controllers\frontend;

use Besnovatyj\Catalog\readModels\CategoryReadRepository;
use Besnovatyj\Catalog\readModels\ProductReadRepository;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ProductController extends Controller
{
    private ProductReadRepository $products;
    private CategoryReadRepository $categories;

    public function __construct(
        $id,
        $module,
        ProductReadRepository $products,
        CategoryReadRepository $categories,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->products = $products;
        $this->categories = $categories;
    }

    /**
     * Страница каталога: активные корневые категории в порядке отображения.
     * Их описание и товары рендерит тема (виджет витрины категории на каждый
     * корень), состав определяется данными дерева, без хардкода slug-ов.
     * @return string
     */
    public function actionIndex(): string
    {
        return $this->render('index', [
            'categories' => $this->categories->getDisplayRoots(),
        ]);
    }

    /**
     * Список продуктов конкретной категории
     * @throws NotFoundHttpException
     */
    public function actionByCategory(string $slug): string
    {
        if (!$category = $this->categories->findBySlug($slug)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $dataProvider = $this->products->getAllByCategory($category);

        return $this->render('by-category', [
            'category' => $category,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Конкретный продукт
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionItem(int $id): string
    {
        if (!$product = $this->products->find($id)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        $prevProduct =  $this->products->findPrevProduct($product);
        $nextProduct = $this->products->findNextProduct($product);

        return $this->render('item', [
            'product' => $product,
            'prevProduct' => $prevProduct,
            'nextProduct' => $nextProduct,
        ]);
    }
}

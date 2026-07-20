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
     * Выбираем вручную несколько категорий и выводим их описание и все их продукты
     * TODO в следующей версии (yii2-cms.loc) или виджеты категорий или выбираем все корни категорий (в следующей версии работа с несколькими корнями гораздо удобнее).
     * NB Текущая реализация не имеет права на существование :)
     * @return string
     */
    public function actionIndex(): string
    {
        $timsonCCategory = $this->categories->findBySlug('all-products');
        $timsonCProductsProvider = $this->products->getAllByCategory($timsonCCategory);

        $aoguCategory = $this->categories->findBySlug('aogu');
        $aoguProductsProvider = $this->products->getAllByCategory($aoguCategory);

        $haserveyCategory = $this->categories->findBySlug('haservey');
        $haserveyProductsProvider = $this->products->getAllByCategory($haserveyCategory);

        return $this->render('index', [
            'timsonCCategory' => $timsonCCategory,
            'timsonCProductsProvider' => $timsonCProductsProvider,
            'aoguCategory' => $aoguCategory,
            'aoguProductsProvider' => $aoguProductsProvider,
            'haserveyCategory' => $haserveyCategory,
            'haserveyProductsProvider' => $haserveyProductsProvider,
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

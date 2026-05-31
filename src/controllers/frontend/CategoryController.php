<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\controllers\frontend;

use Besnovatyj\Catalog\readModels\CategoryReadRepository;
use Besnovatyj\Catalog\readModels\ProductReadRepository;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class CategoryController extends Controller
{

    private $products;
    private $categories;

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
     * Список всех категорий, показ товаров по ситуации
     * @throws NotFoundHttpException
     */
//    public function actionIndex(): string
//    {
//        if (!$category = $this->categories->getRoot()) {
//            throw new NotFoundHttpException('The requested page does not exist.');
//        }
//
//        $dataProvider = $this->products->getAllByCategory($category);
//
//        return $this->render('index', [
//            'category' => $category,
//            'dataProvider' => $dataProvider,
//        ]);
//    }

}

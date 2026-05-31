<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\controllers\frontend;

use Besnovatyj\Catalog\readModels\ProductReadRepository;
use Besnovatyj\Catalog\readModels\TagReadRepository;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class TagController extends Controller
{
    private $products;
    private $tags;

    public function __construct(
        $id,
        $module,
        ProductReadRepository $products,
        TagReadRepository $tags,
        $config = []
    )
    {
        parent::__construct($id, $module, $config);
        $this->products = $products;
        $this->tags = $tags;
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
//    public function actionIndex(int $id): string
//    {
//        if (!$tag = $this->tags->find($id)) {
//            throw new NotFoundHttpException('The requested page does not exist.');
//        }
//
//        $dataProvider = $this->products->getAllByTag($tag);
//
//        return $this->render('tag', [
//            'tag' => $tag,
//            'dataProvider' => $dataProvider,
//        ]);
//    }

}

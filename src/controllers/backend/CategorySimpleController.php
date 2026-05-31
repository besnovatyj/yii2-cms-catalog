<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\controllers\backend;

use Besnovatyj\Catalog\forms\backend\CategoryForm;
use Besnovatyj\Catalog\forms\backend\search\CategorySearch;
use Besnovatyj\Catalog\repositories\CategoryRepository;
use Besnovatyj\Catalog\services\CategorySimpleManageService;
use common\components\controller\ControllerTrait;
use Exception;
use Yii;
use yii\web\Controller;
use yii\web\Response;

class CategorySimpleController extends Controller
{
    use ControllerTrait;

    private CategorySimpleManageService $service;
    private CategoryRepository $repository;

    public function __construct($id, $module, CategorySimpleManageService $service, CategoryRepository $repository, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $id
     * @return string
     */
    public function actionView(int $id): string
    {
        return $this->render('view', [
            'category' => $this->repository->get($id),
        ]);
    }

    /**
     * @param int $id
     * @return Response|string
     */
    public function actionUpdate(int $id): Response|string
    {
        $category = $this->repository->get($id);
        $form = new CategoryForm($category);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->service->edit($category->id, $form);
                return $this->redirect(['view', 'id' => $category->id]);
            } catch (Exception $e) {
                $this->handleDomainException($e);
            }
        }
        return $this->render('update', [
            'model' => $form,
            'category' => $category,
        ]);
    }

}

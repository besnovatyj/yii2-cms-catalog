<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\controllers\backend;

use common\components\controller\ControllerTrait;
use DomainException;
use Besnovatyj\Catalog\services\BrandManageService;
use Besnovatyj\Catalog\entities\Brand;
use Besnovatyj\Catalog\forms\backend\BrandForm;
use Besnovatyj\Catalog\forms\backend\search\BrandSearch;
use Exception;
use Throwable;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class BrandController extends Controller
{
    use ControllerTrait;

    private BrandManageService $service;

    public function __construct($id, $module, BrandManageService $service, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
    }

    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new BrandSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        return $this->render('view', [
            'brand' => $this->findModel($id),
        ]);
    }

    /**
     * @return Response|string
     */
    public function actionCreate(): Response|string
    {
        $form = new BrandForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $brand = $this->service->create($form);
                return $this->redirect(['view', 'id' => $brand->id]);
            } catch (Exception $e) {
                $this->handleDomainException($e, 'Ошибка');
            }
        }
        return $this->render('create', [
            'model' => $form,
        ]);
    }

    /**
     * @param int $id
     * @return Response|string
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id): Response|string
    {
        $brand = $this->findModel($id);

        $form = new BrandForm($brand);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->service->edit($brand->id, $form);
                return $this->redirect(['view', 'id' => $brand->id]);
            } catch (Exception $e) {
                $this->handleDomainException($e, 'Ошибка');
            }
        }
        return $this->render('update', [
            'model' => $form,
            'brand' => $brand,
        ]);
    }

    /**
     * @param int $id
     * @return Response
     */
    public function actionDelete(int $id): Response
    {
        try {
            $this->service->remove($id);
        } catch (Throwable $e) {
            Yii::$app->errorHandler->logException($e);
            if (YII_DEBUG) {
                Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
            } else {
                Yii::$app->session->setFlash('error', 'Ошибка');
            }
        }
        return $this->redirect(['index']);
    }

    /**
     * @param int $id
     * @return Brand the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Brand
    {
        if (($model = Brand::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

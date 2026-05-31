<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\controllers\backend;

use Besnovatyj\Catalog\entities\product\Photo;
use Besnovatyj\Catalog\entities\product\Product;
use Besnovatyj\Catalog\forms\backend\product\PhotosForm;
use Besnovatyj\Catalog\forms\backend\product\ProductForm;
use Besnovatyj\Catalog\forms\backend\search\ProductSearch;
use Besnovatyj\Catalog\image\PhotoImageOwner;
use Besnovatyj\Catalog\repositories\ProductRepository;
use Besnovatyj\Catalog\services\ProductManageService;
use Besnovatyj\Images\helpers\ImageActionsMap;
use common\components\controller\ControllerTrait;
use Exception;
use Throwable;
use Yii;
use yii\base\ExitException;
use yii\base\UnknownPropertyException;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class ProductController extends Controller
{
    use ControllerTrait;

    private ProductRepository $repository;
    private ProductManageService $service;

    public function __construct($id, $module, ProductManageService $service, ProductRepository $repository, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
        $this->repository = $repository;
    }

    public function actions(): array
    {
        return ImageActionsMap::get(
            Photo::class,
            fn(int $id) => new PhotoImageOwner($this->repository->get($id), $this->repository),
        );
    }

    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'activate' => ['POST'],
                    'draft' => ['POST'],

                    'add-image'      => ['POST'],
                    'delete-image'   => ['POST'],
                    'get-images'     => ['POST'],
                    'set-main-image' => ['POST'],
                    'set-new-sort'   => ['POST'],

                    'ajax-save' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): Response|string
    {
        $product = $this->findModel($id);

        $photosForm = new PhotosForm();
        if ($photosForm->load(Yii::$app->request->post()) && $photosForm->validate()) {
            try {
                $this->service->addPhotos($product->id, $photosForm);
                return $this->redirect(['view', 'id' => $product->id]);
            } catch (Exception $e) {
                $this->handleDomainException($e);
            }
        }

        return $this->render('view', [
            'product' => $product,
            'photosForm' => $photosForm,
        ]);
    }

    public function actionCreate(): Response|string
    {
        $form = new ProductForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $product = $this->service->create($form);
                return $this->redirect(['view', 'id' => $product->id]);
            } catch (Exception $e) {
                $this->handleDomainException($e, 'Ошибка');
            }
        }
        return $this->render('create', [
            'model' => $form,
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id): Response|string
    {
        $product = $this->findModel($id);
        $form = new ProductForm($product);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->service->edit($product, $form);
                return $this->redirect(['view', 'id' => $product->id]);
            } catch (Exception $e) {
                $this->handleDomainException($e, 'Ошибка');
            }
        }
        return $this->render('update', [
            'model' => $form,
            'product' => $product,
        ]);
    }

    /**
     * @throws ExitException
     */
    public function actionAjaxSave(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $response = ['status' => 'error'];

        if (Yii::$app->request->isAjax) {
            try {
                // Сохраняемый контент
                $content = Yii::$app->request->post('editor_content') ?: '';
                // Идентификатор редактируемой сущности
                $id = Yii::$app->request->post('model_id') ?: null;
                // Название редактируемого поля сущности
                $fieldName = Yii::$app->request->post('field_name') ?: null;

                if (!empty($id) && !empty($fieldName)) {

                    $product = $this->findModel((int)$id);

                    if (!isset($product->$fieldName)) {
                        throw new UnknownPropertyException('Trying to change non-existent property: ' . $fieldName);
                    }

                    $form = new ProductForm($product);

                    if (($form->$fieldName = urldecode($content)) && $form->validate()) {
                        $this->service->edit($product->id, $form);
                    }

                    $response['status'] = 'success';
                    $response['message'] = 'Saved successfully!';
                    return $response;

                }

                $response['status'] = 'error';
                $response['message'] = 'SAVE NORMALLY BEFORE!';
                return $response;

            } catch (Exception $e) {
                $this->ajaxError($e);
            }
        }

        return $response;
    }


    public function actionDelete($id): Response
    {
        try {
            $this->service->remove($id);
        } catch (Throwable $e) {
            Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
        }
        return $this->redirect(['index']);
    }

    public function actionActivate($id): Response
    {
        try {
            $this->service->activate($id);
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    public function actionDraft($id): Response
    {
        try {
            $this->service->draft($id);
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * @param integer $id
     * @return Product the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Product
    {
        if (($model = Product::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

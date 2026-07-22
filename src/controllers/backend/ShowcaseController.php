<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Catalog\controllers\backend;

use Besnovatyj\Catalog\entities\product\Product;
use Besnovatyj\Catalog\entities\showcase\Showcase;
use Besnovatyj\Catalog\entities\showcase\ShowcaseItem;
use Besnovatyj\Catalog\forms\backend\showcase\ShowcaseForm;
use Besnovatyj\Catalog\forms\backend\showcase\ShowcaseItemForm;
use Besnovatyj\Catalog\repositories\ShowcaseRepository;
use Besnovatyj\Catalog\services\ShowcaseManageService;
use Besnovatyj\Kernel\controller\ControllerTrait;
use Exception;
use Throwable;
use Yii;
use yii\data\ActiveDataProvider;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Контроллер управления витринами товаров
 */
class ShowcaseController extends Controller
{
    use ControllerTrait;

    private ShowcaseRepository $repository;
    private ShowcaseManageService $service;

    public function __construct($id, $module, ShowcaseManageService $service, ShowcaseRepository $repository, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->service = $service;
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'activate' => ['POST'],
                    'draft' => ['POST'],
                    'add-item' => ['POST'],
                    'remove-item' => ['POST'],
                    'reorder-items' => ['POST'],
                    'configure-item' => ['POST'],
                    'toggle-item-status' => ['POST'],
                    'product-photos' => ['GET'],
                ],
            ],
        ];
    }

    /**
     * Список витрин
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Showcase::find()->orderBy(['sort' => SORT_ASC, 'id' => SORT_ASC]),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Просмотр витрины с управлением элементами
     *
     * @param int $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        $showcase = $this->findModel($id);

        $items = ShowcaseItem::find()
            ->andWhere(['showcase_id' => $showcase->id])
            ->with(['product', 'product.photos'])
            ->orderBy(['sort' => SORT_ASC])
            ->all();

        return $this->render('view', [
            'showcase' => $showcase,
            'items' => $items,
            'productsList' => $this->getProductsList($showcase),
        ]);
    }

    /**
     * Создание витрины
     *
     * @return Response|string
     */
    public function actionCreate(): Response|string
    {
        $form = new ShowcaseForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $showcase = $this->service->create($form);
                return $this->redirect(['view', 'id' => $showcase->id]);
            } catch (Exception $e) {
                $this->handleDomainException($e);
            }
        }
        return $this->render('create', [
            'model' => $form,
        ]);
    }

    /**
     * Редактирование витрины
     *
     * @param int $id
     * @return Response|string
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id): Response|string
    {
        $showcase = $this->findModel($id);
        $form = new ShowcaseForm($showcase);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->service->edit($showcase->id, $form);
                return $this->redirect(['view', 'id' => $showcase->id]);
            } catch (Exception $e) {
                $this->handleDomainException($e);
            }
        }
        return $this->render('update', [
            'model' => $form,
            'showcase' => $showcase,
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
            Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
        }
        return $this->redirect(['index']);
    }

    /**
     * @param int $id
     * @return Response
     */
    public function actionActivate(int $id): Response
    {
        try {
            $this->service->activate($id);
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    /**
     * @param int $id
     * @return Response
     */
    public function actionDraft(int $id): Response
    {
        try {
            $this->service->draft($id);
        } catch (Exception $e) {
            Yii::$app->session->setFlash('error', VarDumper::dumpAsString($e->getMessage()));
        }
        return $this->redirect(['view', 'id' => $id]);
    }

    // <editor-fold desc="AJAX: Управление элементами витрины">

    /**
     * Добавить товар в витрину
     *
     * @return array
     */
    public function actionAddItem(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $showcaseId = (int)Yii::$app->request->post('showcase_id');
        $productId = (int)Yii::$app->request->post('product_id');

        try {
            $item = $this->service->addItem($showcaseId, $productId);
            $item->populateRelation('product', Product::findOne($productId));

            return [
                'status' => 'success',
                'item' => $this->serializeItem($item),
            ];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Удалить элемент из витрины
     *
     * @return array
     */
    public function actionRemoveItem(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $itemId = (int)Yii::$app->request->post('item_id');

        try {
            $this->service->removeItem($itemId);
            return ['status' => 'success'];
        } catch (Throwable $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Обновить сортировку элементов
     *
     * @return array
     */
    public function actionReorderItems(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $sortData = Yii::$app->request->post('sort_data', []);

        try {
            $this->service->reorderItems($sortData);
            return ['status' => 'success'];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Настроить элемент витрины (фото, источник заголовка и описания)
     *
     * @return array
     */
    public function actionConfigureItem(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $itemId = (int)Yii::$app->request->post('item_id');

        $form = new ShowcaseItemForm();
        if ($form->load(Yii::$app->request->post(), '') && $form->validate()) {
            try {
                $this->service->configureItem($itemId, $form);
                return ['status' => 'success'];
            } catch (Exception $e) {
                return ['status' => 'error', 'message' => $e->getMessage()];
            }
        }

        return ['status' => 'error', 'message' => 'Validation failed', 'errors' => $form->errors];
    }

    /**
     * Переключить статус элемента
     *
     * @return array
     */
    public function actionToggleItemStatus(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $itemId = (int)Yii::$app->request->post('item_id');

        try {
            $this->service->toggleItemStatus($itemId);
            $item = $this->repository->getItem($itemId);
            return ['status' => 'success', 'itemStatus' => $item->status];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Получить фотографии товара (для выбора в витрине)
     *
     * @param int $product_id
     * @return array
     */
    public function actionProductPhotos(int $product_id): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $product = Product::findOne($product_id);
        if (!$product) {
            return ['status' => 'error', 'message' => 'Product not found'];
        }

        $photos = [];
        foreach ($product->photos as $index => $photo) {
            $photos[] = [
                'index' => $index,
                'thumb' => $photo->getThumbUrl('file', 'admin'),
                'isMain' => $product->main_photo_id === $photo->id,
            ];
        }

        return ['status' => 'success', 'photos' => $photos];
    }

    // </editor-fold>

    /**
     * @param int $id
     * @return Showcase
     * @throws NotFoundHttpException
     */
    protected function findModel(int $id): Showcase
    {
        if (($model = Showcase::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Список товаров, ещё не добавленных в витрину
     *
     * @param Showcase $showcase
     * @return array
     */
    private function getProductsList(Showcase $showcase): array
    {
        $existingProductIds = ShowcaseItem::find()
            ->select('product_id')
            ->andWhere(['showcase_id' => $showcase->id])
            ->column();

        $query = Product::find()->andWhere(['status' => Product::STATUS_ACTIVE]);
        if (!empty($existingProductIds)) {
            $query->andWhere(['not in', 'id', $existingProductIds]);
        }

        return ArrayHelper::map($query->orderBy('name')->with('brand')->asArray()->all(), 'id', function (array $product) {
            $brand = $product['brand']['name'];
            $name = html_entity_decode($product['name_short'], ENT_QUOTES | ENT_HTML5, 'UTF-8')
                ?? html_entity_decode($product['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $weight = (key_exists('weight', $product) && $product['weight'] > 0) ? ' (' . $product['weight'] . ')' : '';
            return '(' . $brand . ') ' . $name . $weight;
        }
        );
    }

    /**
     * Сериализация элемента витрины для JSON-ответа
     *
     * @param ShowcaseItem $item
     * @return array
     */
    private function serializeItem(ShowcaseItem $item): array
    {
        $photos = [];
        if ($item->product) {
            foreach ($item->product->photos as $index => $photo) {
                $photos[] = [
                    'index' => $index,
                    'thumb' => $photo->getThumbUrl('file', 'admin'),
                    'isMain' => $item->product->main_photo_id === $photo->id,
                ];
            }
        }

        return [
            'id' => $item->id,
            'product_id' => $item->product_id,
            'product_name' => $item->product ? $item->product->name : '',
            'product_name_short' => $item->product ? ($item->product->name_short ?: $item->product->name) : '',
            'photo_index' => $item->photo_index,
            'title_source' => $item->title_source,
            'description_source' => $item->description_source,
            'sort' => $item->sort,
            'status' => $item->status,
            'photos' => $photos,
        ];
    }
}

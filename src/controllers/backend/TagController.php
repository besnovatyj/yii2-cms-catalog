<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\controllers\backend;

use Besnovatyj\Catalog\services\TagManageService;
use Besnovatyj\Catalog\entities\Tag;
use Besnovatyj\Catalog\forms\backend\search\TagSearch;
use Besnovatyj\Catalog\forms\backend\TagForm;
use Besnovatyj\Kernel\controller\ControllerTrait;
use Exception;
use Throwable;
use Yii;
use yii\db\StaleObjectException;
use yii\filters\VerbFilter;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class TagController extends Controller
{
    use ControllerTrait;

    private TagManageService $service;

    public function __construct($id, $module, TagManageService $service, $config = [])
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


    public function actionIndex(): string
    {
        $searchModel = new TagSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $id
     * @return string
     * @throws NotFoundHttpException
     */
    public function actionView(int $id): string
    {
        return $this->render('view', [
            'tag' => $this->findModel($id),
        ]);
    }

    /**
     * @return string|Response
     */
    public function actionCreate(): Response|string
    {
        $form = new TagForm();
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $tag = $this->service->create($form);
                return $this->redirect(['view', 'id' => $tag->id]);
            } catch (Exception $e) {
                $this->handleDomainException($e);
            }
        }
        return $this->render('create', [
            'model' => $form,
        ]);
    }

    /**
     * @param integer $id
     * @return Response|string
     * @throws NotFoundHttpException
     */
    public function actionUpdate(int $id): Response|string
    {
        $tag = $this->findModel($id);

        $form = new TagForm($tag);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->service->edit($tag->id, $form);
                return $this->redirect(['view', 'id' => $tag->id]);
            } catch (Exception $e) {
                $this->handleDomainException($e);
            }
        }
        return $this->render('update', [
            'model' => $form,
            'tag' => $tag,
        ]);
    }

    /**
     * @param integer $id
     * @return Response
     */
    public function actionDelete(int $id): Response
    {
        try {
            $this->service->remove($id);
        } catch (Throwable $e) {
            $this->handleDomainException($e);
        }
        return $this->redirect(['index']);
    }

    /**
     * @param integer $id
     * @return Tag the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): Tag
    {
        if (($model = Tag::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }
}

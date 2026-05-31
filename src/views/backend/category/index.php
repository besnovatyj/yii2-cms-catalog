<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Catalog\forms\backend\CategoryForm;
use Besnovatyj\TreeManager\Manager\TreeDataSource;
use Besnovatyj\TreeManager\Manager\TreeWidget;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 * @var string $title
 * @var TreeDataSource $treeDataSource
 */

$this->title = $title;
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="card">
    <div class="card-header"><?= $this->title ?></div>
    <div class="card-body">
        <?= TreeWidget::widget([
            'dataSource' => $treeDataSource,

            'endpoints' => [
                'loadChildren' => Url::to(['/Catalog/backend/category/load-children']),
                'createNode' => Url::to(['/Catalog/backend/category/create']),
                'updateNode' => Url::to(['/Catalog/backend/category/update']),
                'deleteNode' => Url::to(['/Catalog/backend/category/delete']),
                'moveNode' => Url::to(['/Catalog/backend/category/move']),
                'toggleStatus' => Url::to(['/Catalog/backend/category/toggle-status']),
                'checkIntegrity' => Url::to(['/Catalog/backend/category/check-integrity']),
            ],

            'serverForms' => [
                'enabled' => true,
                'display' => 'modal',
                'errorStrategy' => 'both',
                'operations' => [
                    'create' => true,
                    'edit' => true,
                ],
                'getFormUrl' => Url::to(['/Catalog/backend/category/get-form']),
            ],

            'permissions' => [
                'canCreate' => true,
                'canUpdate' => true,
                'canDelete' => true,
                'canMove' => true,
            ],

            'titleField' => 'title',
            'enablePersistence' => true,
            'storageKey' => 'Catalog-category-tree-state',
            'containerOptions' => [
                'class' => 'Catalog-taxonomy-tree-widget',
            ],
        ]) ?>
    </div>
</div>

<?php
// Дополнительные стили
$this->registerCss(<<<CSS
.Catalog-taxonomy-tree-widget {
    min-height: 400px;
}
CSS
);
?>

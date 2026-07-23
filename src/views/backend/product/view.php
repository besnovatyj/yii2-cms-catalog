<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Catalog\entities\product\Product;
use Besnovatyj\Catalog\forms\backend\product\PhotosForm;
use Besnovatyj\Catalog\helpers\ProductHelper;
use Besnovatyj\Images\widgets\upload\Widget;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $product  Product */
/* @var $photosForm PhotosForm */
/* @var $modificationsProvider ActiveDataProvider */

$urlManager = Yii::$app->get('frontendUrlManager'); // TODO

$this->title = html_entity_decode($product->name_short ?? "", ENT_QUOTES | ENT_HTML5, 'UTF-8')
    ?? html_entity_decode($product->name ?? "", ENT_QUOTES | ENT_HTML5, 'UTF-8');
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<p>
    <?php if ($product->isActive()): ?>
        <?= Html::a('Draft', ['draft', 'id' => $product->id], ['class' => 'btn  btn-primary', 'data-method' => 'post']) ?>
    <?php else: ?>
        <?= Html::a('Activate', ['activate', 'id' => $product->id], ['class' => 'btn  btn-success', 'data-method' => 'post']) ?>
    <?php endif; ?>
    <?= Html::a('Update', ['update', 'id' => $product->id], ['class' => 'btn  btn-primary']) ?>
    <?= Html::a('Delete', ['delete', 'id' => $product->id], [
        'class' => 'btn  btn-danger',
        'data' => [
            'confirm' => 'Are you sure you want to delete this item?',
            'method' => 'post',
        ],
    ]) ?>
    <a class="btn  btn-secondary" target="_blank"
       href="<?= $urlManager->createAbsoluteUrl(['/Catalog/product/item/', 'id' => $product->id]) ?>">
        <i class="bi bi-eye"></i>
    </a>
</p>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Common</div>
            <div class="card-body">
                <?= DetailView::widget([
                    'model' => $product,
                    'attributes' => [
                        'id',
                        [
                            'attribute' => 'status',
                            'value' => ProductHelper::statusLabel($product->status),
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'brand_id',
                            'value' => ArrayHelper::getValue($product, 'brand.name'),
                        ],
                        'code',
                        'name',
                        'name_short',
                        [
                            'attribute' => 'category_id',
                            'value' => ArrayHelper::getValue($product, 'category.name'),
                        ],
                        [
                            'label' => 'Other categories',
                            'value' => implode(', ', ArrayHelper::getColumn($product->categories, 'name')),
                        ],
                        [
                            'label' => 'Tags',
                            'value' => implode(', ', ArrayHelper::getColumn($product->tags, 'name')),
                        ],
                        [
                            'attribute' => 'weight',
                            'value' => $product->weight,
                        ],
                    ],
                ]) ?>
            </div>
        </div>
    </div>
</div>

<div class="card rounded-0">
    <div class="card-header">Description</div>
    <div class="card-body">
        <?= Yii::$app->formatter->asHtml($product->description, [
            'Attr.AllowedRel' => array('nofollow'),
            'HTML.SafeObject' => true,
            'Output.FlashCompat' => true,
            'HTML.SafeIframe' => true,
            'URI.SafeIframeRegexp' => '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%',
        ]) ?>
        <hr/>
        <?= Yii::$app->formatter->asHtml($product->description_short, [
            'Attr.AllowedRel' => array('nofollow'),
            'HTML.SafeObject' => true,
            'Output.FlashCompat' => true,
            'HTML.SafeIframe' => true,
            'URI.SafeIframeRegexp' => '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%',
        ]) ?>
    </div>
</div>

<div class="card rounded-0">
    <div class="card-header">SEO</div>
    <div class="card-body">
        <?= DetailView::widget([
            'model' => $product,
            'attributes' => [
                [
                    'attribute' => 'meta.title',
                    'value' => $product->meta->title,
                ],
                [
                    'attribute' => 'meta.description',
                    'value' => $product->meta->description,
                ],
                [
                    'attribute' => 'meta.keywords',
                    'value' => $product->meta->keywords,
                ],
            ],
        ]) ?>
    </div>
</div>

<div class="card">
    <div class="card-header"></div>
    <div class="card-body">
        <?= Widget::widget([
            'ownerId' => $product->id,
            'endpoints' => [
                'getImages' => Url::to(['/Catalog/backend/product/get-images'], true),
                'setNewSort' => Url::to(['/Catalog/backend/product/set-new-sort'], true),
                'upload' => Url::to(['/Catalog/backend/product/add-image'], true),
                'deleteImage' => Url::to(['/Catalog/backend/product/delete-image'], true),
                'setMainImage' => Url::to(['/Catalog/backend/product/set-main-image'], true),
            ],
        ]) ?>
    </div>
    <div class="card-footer">
    </div>
</div>

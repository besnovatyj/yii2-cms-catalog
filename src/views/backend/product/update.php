<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Catalog\entities\product\Product;
use Besnovatyj\Catalog\forms\backend\product\ProductForm;
use yii\web\View;

/* @var $this View */
/* @var $model ProductForm */
/* @var $product Product */

$this->title = 'Update: ' . $product->name_short ?? $product->name;
$this->params['breadcrumbs'][] = ['label' => 'Products', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $product->name_short ?? $product->name, 'url' => ['view', 'id' => $product->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<?= $this->render('_form', [
    'model' => $model,
    'product' => $product,
]) ?>

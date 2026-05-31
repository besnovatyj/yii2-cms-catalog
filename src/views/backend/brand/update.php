<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Catalog\entities\Brand;
use Besnovatyj\Catalog\forms\backend\BrandForm;
use yii\web\View;

/* @var $this View */
/* @var $brand Brand */
/* @var $model BrandForm */

$this->title = 'Update Brand: ' . $brand->name;
$this->params['breadcrumbs'][] = ['label' => 'Brands', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $brand->name, 'url' => ['view', 'id' => $brand->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>

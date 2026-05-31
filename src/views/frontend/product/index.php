<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

/* @var $this View */
/* @var $dataProvider DataProviderInterface */
/* @var $category Category */

use Besnovatyj\Catalog\entities\Category;
use yii\data\DataProviderInterface;
use yii\helpers\Html;
use yii\web\View;

$this->title = 'Catalog';
$this->params['breadcrumbs'][] = $this->title;
?>

<h1><?= Html::encode($this->title) ?></h1>

<?= $this->render('_subcategories', [
    'category' => $category
]) ?>

<?= $this->render('_list', [
    'dataProvider' => $dataProvider
]) ?>



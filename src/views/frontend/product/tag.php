<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Catalog\entities\Tag;
use yii\data\DataProviderInterface;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $dataProvider DataProviderInterface */
/* @var $tag Tag */

$this->title = 'Products with tag ' . $tag->name;

$this->params['breadcrumbs'][] = ['label' => 'Catalog', 'url' => ['index']];
$this->params['breadcrumbs'][] = $tag->name;
?>

<h1>Products with tag &laquo;<?= Html::encode($tag->name) ?>&raquo;</h1>

<hr />

<?= $this->render('_list', [
    'dataProvider' => $dataProvider
]) ?>



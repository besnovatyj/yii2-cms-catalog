<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Catalog\entities\Brand;
use yii\data\DataProviderInterface;
use yii\helpers\Html;
use yii\web\View;

/**
 * Демо-вьюха страницы бренда: товары бренда.
 *
 * @var View                  $this
 * @var Brand                 $brand
 * @var DataProviderInterface $dataProvider
 */

$this->title = $brand->getSeoTitle();

$this->registerMetaTag(['name' => 'description', 'content' => $brand->meta->description]);
$this->registerMetaTag(['name' => 'keywords', 'content' => $brand->meta->keywords]);

$this->params['breadcrumbs'][] = ['label' => 'Каталог', 'url' => ['index']];
$this->params['breadcrumbs'][] = $brand->name;
?>
<div class="container py-4">
    <h1 class="h3 mb-4"><?= Html::encode($brand->name) ?></h1>

    <?= $this->render('_list', ['dataProvider' => $dataProvider]) ?>
</div>

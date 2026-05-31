<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Catalog\entities\showcase\Showcase;
use Besnovatyj\Catalog\forms\backend\showcase\ShowcaseForm;
use yii\web\View;

/* @var $this View */
/* @var $model ShowcaseForm */
/* @var $showcase Showcase */

$this->title = 'Редактировать: ' . $showcase->name;
$this->params['breadcrumbs'][] = ['label' => 'Витрины', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $showcase->name, 'url' => ['view', 'id' => $showcase->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>

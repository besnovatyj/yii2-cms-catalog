<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Catalog\forms\backend\showcase\ShowcaseForm;
use yii\web\View;

/* @var $this View */
/* @var $model ShowcaseForm */

$this->title = 'Создать витрину';
$this->params['breadcrumbs'][] = ['label' => 'Витрины', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('_form', [
    'model' => $model,
]) ?>

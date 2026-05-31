<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Catalog\entities\Characteristic;
use Besnovatyj\Catalog\forms\backend\CharacteristicForm;
use yii\web\View;

/* @var $this View */
/* @var $characteristic Characteristic */
/* @var $model CharacteristicForm */

$this->title = 'Update Characteristic: ' . $characteristic->name;
$this->params['breadcrumbs'][] = ['label' => 'Characteristics', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $characteristic->name, 'url' => ['view', 'id' => $characteristic->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<?= $this->render('_form', [
    'model' => $model,
    'characteristic' => $characteristic,
]) ?>

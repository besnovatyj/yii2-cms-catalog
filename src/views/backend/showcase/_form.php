<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Catalog\forms\backend\showcase\ShowcaseForm;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model ShowcaseForm */

?>

<?php $form = ActiveForm::begin(); ?>

<div class="card">
    <div class="card-header">Витрина</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'sort')->textInput(['type' => 'number']) ?>
                <?= $form->field($model, 'category_id')->dropDownList($model->categoriesList(), [
                    'prompt' => '— свободная витрина (не привязана к категории) —',
                ])->hint('Если выбрать категорию, её страница каталога будет показывать товары из этой витрины (порядок, фото, поля берутся отсюда).') ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="d-grid gap-2">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

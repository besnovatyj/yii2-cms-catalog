<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Catalog\forms\backend\BrandForm;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model BrandForm */
/* @var $form ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>
<div class="card">
    <div class="card-header">Common</div>
    <div class="card-body">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="card-footer">
        <div class="d-grid gap-2">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">SEO</div>
    <div class="card-body">
        <?= $form->field($model->meta, 'title')->textInput() ?>
        <?= $form->field($model->meta, 'description')->textarea(['rows' => 2]) ?>
        <?= $form->field($model->meta, 'keywords')->textInput() ?>
    </div>
    <div class="card-footer">
        <div class="d-grid gap-2">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

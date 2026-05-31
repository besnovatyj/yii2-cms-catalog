<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Catalog\forms\backend\CharacteristicForm;
use yii\helpers\Html;
use yii\web\View;
use yii\bootstrap5\ActiveForm;

/* @var $this View */
/* @var $model CharacteristicForm */
/* @var $form ActiveForm */
?>

<?php $form = ActiveForm::begin(); ?>
<div class="card">
    <div class="card-header"><?= $this->title ?></div>
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-md-6">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-12 col-md-6">
                <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-12 col-md-6">
                <?= $form->field($model, 'type')->dropDownList($model->typesList()) ?>
            </div>
            <div class="col-12 col-md-6">
                <?= $form->field($model, 'sort')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-12 col-md-6">
                <?= $form->field($model, 'required')->checkbox() ?>
            </div>
            <div class="col-12 col-md-6">
                <?= $form->field($model, 'default')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-12">
                <?= $form->field($model, 'textVariants')->textarea(['rows' => 6]) ?>
            </div>
        </div>
    </div>
    <div class="card-footer">
        <div class="d-grid gap-2">
            <?= Html::submitButton('Save', ['class' => 'btn  btn-block btn-success']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Catalog\forms\backend\TagForm;
use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;
use yii\web\View;

/* @var $this View */
/* @var $model TagForm */
/* @var $form ActiveForm */
?>


<?php $form = ActiveForm::begin(); ?>
<div class="card">
    <div class="card-header"><?= $this->title ?></div>
    <div class="card-body">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="card-footer">
        <div class="d-grid gap-2">
            <?= Html::submitButton('Save', ['class' => 'btn  btn-block btn-success']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

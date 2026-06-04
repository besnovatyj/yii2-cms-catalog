<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Catalog\entities\product\Product;
use Besnovatyj\Catalog\forms\backend\product\ProductForm;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $model ProductForm */
/* @var $product Product */

?>

<?php $form = ActiveForm::begin(); ?>

<div class="card">
    <div class="card-header">Common</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'name_short')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'brandId')->dropDownList($model->brandsList()) ?>
                <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
                <?= $form->field($model, 'weight')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <?php
        if (!isset($product)) {
            echo '<div class="alert alert-danger" role="alert">Перед заполнением сохраните.</div>';
        } else {
            // TODO создавать папку при создании. При удалении удалять.
            $editorConfig = [];
            $editorConfig['language'] = 'ru';
            $editorConfig['fmDefaultPath'] = '/static/origin/Catalog/Products/' . $product->id;
            echo $form->field($model, 'description')->widget(\Besnovatyj\File\widgets\CkeditorCustomWidget::class, $editorConfig);
        }
        ?>

        <?php
        if (!isset($product)) {
            echo '<div class="alert alert-danger" role="alert">Перед заполнением сохраните.</div>';
        } else {
            // TODO создавать папку при создании. При удалении удалять.
            $editorConfig = [];
            $editorConfig['language'] = 'ru';
            $editorConfig['fmDefaultPath'] = '/static/origin/Catalog/Products/' . $product->id;
            echo $form->field($model, 'description_short')->widget(\Besnovatyj\File\widgets\CkeditorCustomWidget::class, $editorConfig);
        }
        ?>
    </div>
    <div class="card-footer">
        <div class="d-grid gap-2">
            <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Categories</div>
            <div class="card-body">
                <?= $form->field($model->categoriesForm, 'main')->dropDownList($model->categoriesForm->categoriesList(), ['prompt' => '']) ?>
                <?= $form->field($model->categoriesForm, 'others')->checkboxList($model->categoriesForm->categoriesList()) ?>
            </div>
            <div class="card-footer">
                <div class="d-grid gap-2">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Characteristics</div>
            <div class="card-body">
                <?php foreach ($model->valueForms as $i => $value): ?>
                    <?php if ($variants = $value->variantsList()): ?>
                        <?= $form->field($value, '[' . $i . ']value')->dropDownList($variants, ['prompt' => '']) ?>
                    <?php else: ?>
                        <?= $form->field($value, '[' . $i . ']value')->textInput() ?>
                    <?php endif ?>
                <?php endforeach; ?>
            </div>
            <div class="card-footer">
                <div class="d-grid gap-2">
                    <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-header">SEO</div>
            <div class="card-body">
                <?= $form->field($model->metaForm, 'title')->textInput() ?>
                <?= $form->field($model->metaForm, 'description')->textarea(['rows' => 2]) ?>
                <?= $form->field($model->metaForm, 'keywords')->textInput() ?>
            </div>
            <div class="card-footer">
                <div class="d-grid gap-2">
                    <?= Html::submitButton('Save', ['class' => 'btn  btn-block btn-success']) ?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="card">
            <div class="card-header">Tags</div>
            <div class="card-body">
                <?= $form->field($model->tagsForm, 'newTagsNames')->widget(\Besnovatyj\Select2\Select2Widget::class, [
                    'endpoint' => \yii\helpers\Url::to(['/Catalog/backend/tag/search-endpoint'], true),
//                        'options' => ['class' => 'form-control'],
                    'options' => ['class' => ''],
                ]) ?>
            </div>
            <div class="card-footer">
                <div class="d-grid gap-2">
                    <?= Html::submitButton('Save', ['class' => 'btn  btn-block btn-success']) ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

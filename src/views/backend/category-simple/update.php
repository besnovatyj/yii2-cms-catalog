<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Catalog\entities\Category;
use Besnovatyj\Catalog\forms\backend\CategoryForm;
use Besnovatyj\Editor\EditorWidget;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/* @var $this View */
/* @var $form ActiveForm */
/* @var $category Category */
/* @var $model CategoryForm */

$this->title = 'Update Category: ' . $category->name;
$this->params['breadcrumbs'][] = ['label' => 'Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $category->name, 'url' => ['view', 'id' => $category->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<?php $form = ActiveForm::begin(); ?>
<div class="card">
    <div class="card-header">Common</div>
    <div class="card-body">
        <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'slug')->textInput(['maxlength' => true]) ?>
        <?php
        if ($model->isNewRecord()) {
            echo '<div class="alert alert-danger" role="alert">Перед заполнением сохраните.</div>';
        } else {
            // TODO создавать папку при создании. При удалении удалять.
            $editorConfig = [];
            $editorConfig['language'] = 'ru';
            $editorConfig['fmDefaultPath'] = '/static/origin/Catalog/Categories/' . $model->nodeId;
            echo $form->field($model, 'description')->widget(EditorWidget::class, $editorConfig);
        }
        ?>
    </div>
    <div class="card-footer">
        <div class="d-grid gap-2">
            <?= Html::submitButton('Save', ['class' => 'btn  btn-block btn-success']) ?>
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
            <?= Html::submitButton('Save', ['class' => 'btn  btn-block btn-success']) ?>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>


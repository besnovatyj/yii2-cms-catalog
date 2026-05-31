<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Catalog\entities\Characteristic;
use Besnovatyj\Catalog\helpers\CharacteristicHelper;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/* @var $this View */
/* @var $characteristic Characteristic */

$this->title = $characteristic->name;
$this->params['breadcrumbs'][] = ['label' => 'Characteristics', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<p>
    <?= Html::a('Update', ['update', 'id' => $characteristic->id], ['class' => 'btn  btn-primary']) ?>
    <?= Html::a('Delete', ['delete', 'id' => $characteristic->id], [
        'class' => 'btn  btn-danger',
        'data' => [
            'confirm' => 'Are you sure you want to delete this item?',
            'method' => 'post',
        ],
    ]) ?>
</p>

<div class="card">
    <div class="card-header"><?= $this->title ?></div>
    <div class="card-body">
        <?= DetailView::widget([
            'model' => $characteristic,
            'attributes' => [
                'id',
                'name',
                [
                    'attribute' => 'type',
                    'value' => CharacteristicHelper::typeName($characteristic->type),
                ],
                'sort',
                'required:boolean',
                'default',
                [
                    'attribute' => 'variants',
                    'value' => implode(PHP_EOL, $characteristic->variants),
                    'format' => 'ntext',
                ],
                'slug',
            ],
        ]) ?>
    </div>
</div>

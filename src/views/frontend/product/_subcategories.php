<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Catalog\entities\Category;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $category Category */

?>

<?php if ($category->children): ?>
    <div class="panel panel-default">
        <div class="panel-body">
            <?php foreach ($category->children as $child): ?>
                <a href="<?= Html::encode(Url::to(['/Catalog/product/by-category', 'slug' => $child->slug])) ?>"><?= Html::encode($child->name) ?></a> &nbsp;
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

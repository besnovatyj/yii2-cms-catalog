<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Catalog\entities\Characteristic;
use Besnovatyj\Catalog\entities\showcase\ShowcaseItem;
use Besnovatyj\Catalog\helpers\ShowcaseHelper;
use yii\helpers\Html;
use yii\helpers\Json;

/* @var $item ShowcaseItem */
/* @var $characteristics Characteristic[] */

$product = $item->product;
$photos = $product ? $product->photos : [];
$displayPhoto = $item->getDisplayPhoto();
$selectedSlugs = $item->getDisplayCharacteristicSlugs();

$itemData = Json::encode([
    'id' => $item->id,
    'product_id' => $item->product_id,
    'photo_index' => $item->photo_index,
    'display_characteristics' => $selectedSlugs,
    'custom_title' => $item->custom_title,
    'status' => $item->status,
]);
?>

<div class="showcase-item border-bottom p-3" data-item-id="<?= $item->id ?>" data-item-config='<?= $itemData ?>'>
    <div class="d-flex align-items-start gap-3">
        <!-- Drag handle -->
        <div class="showcase-item-handle d-flex align-items-center" style="cursor: grab; padding: 8px 4px;" title="Перетащите для сортировки">
            <i class="bi bi-grip-vertical fs-4 text-muted"></i>
        </div>

        <!-- Фото превью -->
        <div class="showcase-item-photo flex-shrink-0" style="width: 100px; height: 70px; overflow: hidden; background: #f8f9fa; border-radius: 4px;">
            <?php if ($displayPhoto): ?>
                <img src="<?= $displayPhoto->getThumbUrl('file', 'admin') ?>"
                     alt=""
                     style="width: 100%; height: 100%; object-fit: cover;">
            <?php else: ?>
                <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                    <i class="bi bi-image fs-4"></i>
                </div>
            <?php endif; ?>
        </div>

        <!-- Информация -->
        <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <strong><?= Html::encode($product ? $product->name : 'Товар удалён') ?></strong>
                    <?php if ($item->custom_title): ?>
                        <br><small class="text-muted">Кастомный заголовок: <?= Html::encode($item->custom_title) ?></small>
                    <?php endif; ?>
                    <?php if ($item->photo_index !== null): ?>
                        <br><small class="text-info">Фото #<?= $item->photo_index ?></small>
                    <?php else: ?>
                        <br><small class="text-muted">Главное фото</small>
                    <?php endif; ?>
                    <?php if (!empty($selectedSlugs)): ?>
                        <br><small class="text-secondary">
                            Характеристики: <?= implode(', ', array_map(static function (string $slug) use ($characteristics) {
                                foreach ($characteristics as $c) {
                                    if ($c->slug === $slug) {
                                        return $c->name;
                                    }
                                }
                                return $slug;
                            }, $selectedSlugs)) ?>
                        </small>
                    <?php endif; ?>
                </div>
                <div class="d-flex gap-1 align-items-center">
                    <span class="showcase-item-status">
                        <?= ShowcaseHelper::itemStatusLabel($item->status) ?>
                    </span>
                    <button class="btn btn-sm btn-outline-secondary showcase-item-toggle-status" title="Вкл/Выкл">
                        <i class="bi bi-power"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-primary showcase-item-configure" title="Настроить">
                        <i class="bi bi-gear"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger showcase-item-remove" title="Удалить">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Панель настроек (скрыта по умолчанию) -->
    <div class="showcase-item-settings mt-3" style="display: none;">
        <div class="card bg-light">
            <div class="card-body">
                <div class="row g-3">
                    <!-- Выбор фото -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Фото</label>
                        <div class="showcase-item-photos d-flex flex-wrap gap-1">
                            <div class="form-check">
                                <input class="form-check-input showcase-photo-radio" type="radio"
                                       name="photo_index_<?= $item->id ?>" value=""
                                    <?= $item->photo_index === null ? 'checked' : '' ?>>
                                <label class="form-check-label">Главное</label>
                            </div>
                            <?php foreach ($photos as $index => $photo): ?>
                                <div class="form-check">
                                    <input class="form-check-input showcase-photo-radio" type="radio"
                                           name="photo_index_<?= $item->id ?>" value="<?= $index ?>"
                                        <?= $item->photo_index === $index ? 'checked' : '' ?>>
                                    <label class="form-check-label">
                                        <img src="<?= $photo->getThumbUrl('file', 'admin') ?>"
                                             style="width: 50px; height: 35px; object-fit: cover; border-radius: 3px;"
                                             alt="#<?= $index ?>">
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Характеристики -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Характеристики</label>
                        <?php foreach ($characteristics as $c): ?>
                            <div class="form-check">
                                <input class="form-check-input showcase-characteristic-checkbox" type="checkbox"
                                       value="<?= Html::encode($c->slug) ?>"
                                    <?= in_array($c->slug, $selectedSlugs, true) ? 'checked' : '' ?>>
                                <label class="form-check-label"><?= Html::encode($c->name) ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Кастомный заголовок -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Кастомный заголовок</label>
                        <input type="text" class="form-control showcase-item-custom-title"
                               value="<?= Html::encode($item->custom_title ?? '') ?>"
                               placeholder="Оставьте пустым для name_short">
                    </div>
                </div>

                <div class="mt-3">
                    <button class="btn btn-sm btn-primary showcase-item-save-config">
                        <i class="bi bi-check-lg"></i> Сохранить настройки
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

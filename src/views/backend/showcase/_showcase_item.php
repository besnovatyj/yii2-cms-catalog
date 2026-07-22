<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

use Besnovatyj\Catalog\entities\product\Product;
use Besnovatyj\Catalog\entities\showcase\ShowcaseItem;
use Besnovatyj\Catalog\helpers\ShowcaseHelper;
use yii\helpers\Html;
use yii\helpers\Json;

/* @var $item ShowcaseItem */

$product = $item->product;
$photos = $product ? $product->photos : [];
$displayPhoto = $item->getDisplayPhoto();

$titleSources = Product::titleSources();
$descriptionSources = Product::descriptionSources();
$titleKey = $item->title_source ?: Product::TITLE_SOURCE_DEFAULT;
$descriptionKey = $item->description_source ?: Product::DESCRIPTION_SOURCE_DEFAULT;

$itemData = Json::encode([
    'id' => $item->id,
    'product_id' => $item->product_id,
    'photo_index' => $item->photo_index,
    'title_source' => $item->title_source,
    'description_source' => $item->description_source,
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
                    <br><small class="text-muted">Заголовок: <?= Html::encode($titleSources[$titleKey] ?? $titleKey) ?></small>
                    <br><small class="text-muted">Описание: <?= Html::encode($descriptionSources[$descriptionKey] ?? $descriptionKey) ?></small>
                    <?php if ($item->photo_index !== null): ?>
                        <br><small class="text-info">Фото #<?= $item->photo_index ?></small>
                    <?php else: ?>
                        <br><small class="text-muted">Главное фото</small>
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

                    <!-- Источник заголовка -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Заголовок из поля</label>
                        <select class="form-select showcase-item-title-source">
                            <?php foreach ($titleSources as $key => $label): ?>
                                <option value="<?= Html::encode($key) ?>" <?= $titleKey === $key ? 'selected' : '' ?>>
                                    <?= Html::encode($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Источник описания -->
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Описание из поля</label>
                        <select class="form-select showcase-item-description-source">
                            <?php foreach ($descriptionSources as $key => $label): ?>
                                <option value="<?= Html::encode($key) ?>" <?= $descriptionKey === $key ? 'selected' : '' ?>>
                                    <?= Html::encode($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
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

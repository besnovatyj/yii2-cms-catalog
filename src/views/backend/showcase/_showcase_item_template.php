<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

/**
 * HTML-шаблон элемента витрины для клиентского рендеринга через JavaScript.
 * Плейсхолдеры {{...}} заменяются в TypeScript при добавлении нового элемента.
 */
?>
<div class="showcase-item border-bottom p-3" data-item-id="{{id}}" data-item-config='{{config}}'>
    <div class="d-flex align-items-start gap-3">
        <div class="showcase-item-handle d-flex align-items-center" style="cursor: grab; padding: 8px 4px;" title="Перетащите для сортировки">
            <i class="bi bi-grip-vertical fs-4 text-muted"></i>
        </div>

        <div class="showcase-item-photo flex-shrink-0" style="width: 100px; height: 70px; overflow: hidden; background: #f8f9fa; border-radius: 4px;">
            <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                <i class="bi bi-image fs-4"></i>
            </div>
        </div>

        <div class="flex-grow-1">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <strong>{{product_name}}</strong>
                    <br><small class="text-muted">Главное фото</small>
                </div>
                <div class="d-flex gap-1 align-items-center">
                    <span class="showcase-item-status">
                        <span class="badge bg-success">Вкл</span>
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

    <div class="showcase-item-settings mt-3" style="display: none;">
        <div class="card bg-light">
            <div class="card-body">
                <p class="text-muted">Сохраните и откройте витрину заново для настройки фото и характеристик.</p>
            </div>
        </div>
    </div>
</div>

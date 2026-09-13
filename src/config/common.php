<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Catalog\Module;
use Besnovatyj\Validators\SlugValidator;

/**
 * Yii2-конфиг модуля для движка yiisoft/config (группа `common` — общий для всех приложений).
 *
 * Объявляется через `extra.config-plugin`, собирается modman в merge-plan и мёржится в рантайме.
 * Содержит регистрацию модуля. Меню (adminMenu) и миграции остаются вкладами modman. Значения берутся
 * из статических методов {@see Module} — единый источник, без дублирования.
 *
 * Строковые правила капитализированы под id модуля 'Catalog'.
 *
 * ВНИМАНИЕ: frontend-контроллёры каталога (CategoryController/ProductController) сейчас заглушки —
 * маршруты объявлены, но экшены надо реализовать.
 */
return [
    'modules' => [
        Module::moduleId() => array_merge(
            ['class' => Module::class],
            Module::moduleConfig(),
            ['version' => Module::moduleVersion()],
        ),
    ],
    'components' => [
        'frontendUrlManager' => [
            'rules' => [
                // Роуты — по реальным экшенам ProductController (index / item / by-category); прежние
                // 'Catalog/category/index' и 'Catalog/product/view' не существовали.
                'catalog'                                           => 'Catalog/product/index',
                // Бренд — в своём сегменте, с <id> не конкурирует: слаг ANY (как у BrandForm).
                'catalog/brand/<slug:' . SlugValidator::SLUG_ANY . '>' => 'Catalog/product/brand',
                'catalog/<id:\d+>'                                  => 'Catalog/product/item',
                'catalog/<slug:' . SlugValidator::SLUG_STRICT . '>' => 'Catalog/product/by-category',
            ],
        ],
    ],
];

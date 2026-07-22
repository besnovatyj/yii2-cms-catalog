<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

use Besnovatyj\Catalog\Module;
use Besnovatyj\Catalog\urls\CategoryUrlRule;

/**
 * Yii2-конфиг модуля для движка yiisoft/config (группа `common` — общий для всех приложений).
 *
 * Объявляется через `extra.config-plugin`, собирается modman в merge-plan и мёржится в рантайме.
 * Содержит регистрацию модуля. Меню (adminMenu) и миграции остаются вкладами modman. Значения берутся
 * из статических методов {@see Module} — единый источник, без дублирования.
 *
 * URL-правила — вклад в компонент `frontendUrlManager` (группа `common`, см. README_Yii2_Modules.md).
 * ЧПУ дерева категорий реализовано классом-правилом {@see CategoryUrlRule} (UrlRuleInterface) — он
 * DI-конструируется контейнером. Для класс-правил обязателен `frontendUrlManager.cache = false`
 * (см. common/config/components.php). Строковые правила капитализированы под id модуля 'Catalog'.
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
                'catalog'                => 'Catalog/category/index',
                //['class' => CategoryUrlRule::class], // catalog/<slug-путь дерева> ↔ Catalog/category/view
                'catalog/<id:\d+>'       => 'Catalog/product/view',
            ],
        ],
    ],
    // L2-bootstrap: инвалидация кэша ЧПУ-путей категорий при правках дерева (см. Bootstrap).
    'bootstrap' => array_values(Module::bootstrapClasses()),
];

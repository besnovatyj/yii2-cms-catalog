<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Catalog;

use Besnovatyj\Catalog\entities\Category;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\caching\TagDependency;
use yii\db\ActiveRecord;

/**
 * Bootstrap каталога: инвалидация кэша ЧПУ-путей категорий при изменении дерева.
 *
 * {@see \Besnovatyj\Catalog\urls\CategoryUrlRule} кэширует соответствие «slug-путь ↔ id» с тегом
 * `categories`. Категории пишутся через tree-manager (NestedSetsRepository) обычным AR `->save()`,
 * поэтому ловим AR-события `Category` и сбрасываем весь тег `categories`. Инвалидация «крупным
 * помолом» (весь тег) покрывает и переименование слага, и перемещение узла — у потомков путь тоже
 * меняется, а флаш тега пересоберёт все пути при следующем обращении.
 *
 * Bootstrap глобальный (L2, гейт modman): выполняется во всех приложениях. Категории редактируются в
 * backend — событие срабатывает там, а кэш `apcu` общий, поэтому фронт получает уже свежие ЧПУ.
 *
 * @see packages/!README/README_Yii2_Modules.md — «Правила-классы (UrlRuleInterface)».
 */
final class Bootstrap implements BootstrapInterface
{
    public function bootstrap($app): void
    {
        $invalidate = static function (): void {
            if (Yii::$app->has('cache')) {
                TagDependency::invalidate(Yii::$app->cache, ['categories']);
            }
        };

        Event::on(Category::class, ActiveRecord::EVENT_AFTER_INSERT, $invalidate);
        Event::on(Category::class, ActiveRecord::EVENT_AFTER_UPDATE, $invalidate);
        Event::on(Category::class, ActiveRecord::EVENT_AFTER_DELETE, $invalidate);
    }
}

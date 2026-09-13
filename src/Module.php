<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog;

use Besnovatyj\Kernel\module\CmsModule;
use Besnovatyj\Contracts\dashboard\DashboardWidgetDescriptor;
use Besnovatyj\Contracts\dashboard\ProvidesDashboardWidgets;
use Besnovatyj\Contracts\module\DeclaresModule;
use Besnovatyj\Contracts\module\ProvidesAdminMenu;
use Besnovatyj\Contracts\module\ProvidesDependencies;
use Besnovatyj\Contracts\module\ProvidesDirectories;
use Besnovatyj\Contracts\module\ProvidesMigrations;
use Besnovatyj\Contracts\module\ProvidesOptions;
use Besnovatyj\Contracts\tags\TaggableProvider;
use Besnovatyj\Contracts\tags\TagSource;
use Besnovatyj\Catalog\entities\product\Product;
use Besnovatyj\Catalog\readModels\ProductReadRepository;
use Besnovatyj\Catalog\widgets\dashboard\ProductsCountTile;

class Module  extends CmsModule implements
    DeclaresModule, ProvidesAdminMenu,
    ProvidesDependencies,  ProvidesDirectories,
    ProvidesMigrations, ProvidesOptions,
    ProvidesDashboardWidgets, TaggableProvider
{
    public const bool EDITABLE = true;
    public const string VERSION = '1.0.0';
    public const string MODULE_ID = 'Catalog';

    public static function moduleId(): string { return self::MODULE_ID; }
    public static function moduleVersion(): string { return self::VERSION; }
    public static function isEditable(): bool { return self::EDITABLE; }
    public static function adminMenu(): array { return require __DIR__.'/config/adminMenu.php'; }
    public static function moduleConfig(): array { return require __DIR__.'/config/config.php'; }
    public static function options(): array { return require __DIR__.'/config/options.php'; }
    public static function dependencies(): array { return require __DIR__.'/config/dependencies.php'; }
    public static function migrationPath(): string { return __DIR__.'/migrations'; }
    public static function migrationNamespace(): ?string { return __NAMESPACE__.'\\migrations'; }
    public static function directories(): array { return ['@static/origin/Catalog','@static/cache/Catalog'];}

    /** @return DashboardWidgetDescriptor[] */
    public static function dashboardWidgets(): array
    {
        return [
            new DashboardWidgetDescriptor(
                id: self::MODULE_ID . '.productsCount',
                title: 'Товары',
                tileClass: ProductsCountTile::class,
                iconClass: 'bi bi-box-seam',
                priority: 200,
            ),
        ];
    }

    /**
     * Товары — участники общего словаря тегов. Реализация {@see TaggableProvider}; вызывается модулем
     * тегов для страницы `/tag/<slug>` и облака.
     *
     * @return TagSource[]
     */
    public function tagSources(): array
    {
        return [
            new TagSource(Product::tagType(), 'Товары', 'bi bi-box-seam'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function visibleTaggedIds(string $type, array $ids): array
    {
        return match ($type) {
            Product::tagType() => new ProductReadRepository()->visibleIds($ids),
            default => [],
        };
    }

    /**
     * {@inheritdoc}
     */
    public function taggedItems(string $type, array $ids): iterable
    {
        return match ($type) {
            Product::tagType() => new ProductReadRepository()->taggedItems($ids),
            default => [],
        };
    }
}

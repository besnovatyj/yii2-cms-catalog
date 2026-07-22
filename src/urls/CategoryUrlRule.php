<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Catalog\urls;

use Besnovatyj\Catalog\readModels\CategoryReadRepository;
use Yii;
use yii\base\BaseObject;
use yii\base\InvalidArgumentException;
use yii\caching\CacheInterface;
use yii\caching\TagDependency;
use yii\web\UrlNormalizerRedirectException;
use yii\web\UrlRuleInterface;

/**
 * ЧПУ-правило дерева категорий каталога (по мотивам ElisDN yii2-shop).
 *
 * Двунаправленно: `catalog/<slug-предка>/.../<slug>` ↔ внутренний роут `Catalog/category/view` c `id`.
 * Slug-путь строится из дерева (nested sets), результат кэшируется (инвалидация по тегу `categories`),
 * при неканоническом пути бросается 301-редирект на канонический адрес.
 *
 * Правило DI-конструируемо: подключается как `['class' => CategoryUrlRule::class]` в
 * `components.frontendUrlManager.rules` (config-plugin группы `common`), зависимость
 * {@see CategoryReadRepository} внедряет контейнер. Кэш правил UrlManager для таких правил ОБЯЗАН быть
 * выключен (`frontendUrlManager.cache = false`) — объект-правило с сервисами не сериализуется.
 *
 * Регистр роута: внутренний роут начинается с реального id модуля с заглавной — `Catalog/...`.
 */
final class CategoryUrlRule extends BaseObject implements UrlRuleInterface
{
    /** Префикс ЧПУ (первый сегмент URL); после него — путь слагов дерева. */
    public string $prefix = 'catalog';

    /** Внутренний роут (модуль/контроллёр/экшен) с id категории. */
    public string $route = 'Catalog/category/view';

    private CategoryReadRepository $repository;
    private CacheInterface $cache;

    public function __construct(CategoryReadRepository $repository, $config = [])
    {
        parent::__construct($config);
        $this->repository = $repository;
        $this->cache = Yii::$app->cache;
    }

    /**
     * @throws UrlNormalizerRedirectException
     */
    public function parseRequest($manager, $request): array|false
    {
        if (!preg_match('#^' . $this->prefix . '/(.*[a-z])$#is', $request->pathInfo, $matches)) {
            return false;
        }
        $path = $matches[1];

        $result = $this->cache->getOrSet(['category_route', 'path' => $path], function () use ($path) {
            if (!$category = $this->repository->findBySlug($this->leafSlug($path))) {
                return ['id' => null, 'path' => null];
            }
            return ['id' => $category->id, 'path' => $this->repository->pathTo($category)];
        }, null, new TagDependency(['tags' => ['categories']]));

        if (empty($result['id'])) {
            return false;
        }

        // Путь не канонический (напр. по слагу листа перешли, но предки другие) → 301 на канонический.
        if ($path !== $result['path']) {
            throw new UrlNormalizerRedirectException([$this->route, 'id' => $result['id']], 301);
        }

        return [$this->route, ['id' => $result['id']]];
    }

    public function createUrl($manager, $route, $params): string|false
    {
        if ($route !== $this->route) {
            return false;
        }
        if (empty($params['id'])) {
            throw new InvalidArgumentException('Empty id.');
        }
        $id = $params['id'];

        $path = $this->cache->getOrSet(['category_route', 'id' => $id], function () use ($id) {
            return ($category = $this->repository->find((int)$id)) ? $this->repository->pathTo($category) : null;
        }, null, new TagDependency(['tags' => ['categories']]));

        if (!$path) {
            throw new InvalidArgumentException('Undefined id.');
        }

        $url = $this->prefix . '/' . $path;
        unset($params['id']);
        if ($params !== [] && ($query = http_build_query($params)) !== '') {
            $url .= '?' . $query;
        }

        return $url;
    }

    private function leafSlug(string $path): string
    {
        $chunks = explode('/', $path);
        return end($chunks);
    }
}

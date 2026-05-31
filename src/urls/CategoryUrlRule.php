<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace frontend\urls;

use Besnovatyj\Catalog\entities\Category;
use Besnovatyj\Catalog\readModels\CategoryReadRepository;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\BaseObject;
use yii\caching\CacheInterface;
use yii\caching\TagDependency;
use yii\helpers\ArrayHelper;
use yii\web\UrlNormalizerRedirectException;
use yii\web\UrlRuleInterface;

class CategoryUrlRule extends BaseObject implements UrlRuleInterface
{
    public string $prefix = 'catalog';

    private CategoryReadRepository $repository;
    private CacheInterface $cache;

    public function __construct(CategoryReadRepository $repository, $config = [])
    {
        parent::__construct($config);
        $this->repository = $repository;
        $this->cache = Yii::$app->cache;
    }

    public function parseRequest($manager, $request): false|array
    {
        if (preg_match('#^' . $this->prefix . '/(.*[a-z])$#is', $request->pathInfo, $matches)) {
            $path = $matches['1'];

            $result = $this->cache->getOrSet(['category_route', 'path' => $path], function () use ($path) {
                if (!$category = $this->repository->findBySlug($this->getPathSlug($path))) {
                    return ['id' => null, 'path' => null];
                }
                return ['id' => $category->id, 'path' => $this->getCategoryPath($category)];
            }, null, new TagDependency(['tags' => ['categories']]));

            if (empty($result['id'])) {
                return false;
            }

            if ($path != $result['path']) {
                throw new UrlNormalizerRedirectException(['catalog/catalog/category', 'id' => $result['id']], 301);
            }

            return ['catalog/catalog/category', ['id' => $result['id']]];
        }
        return false;
    }

    public function createUrl($manager, $route, $params): false|string
    {
        if ($route == 'catalog/catalog/category') {
            if (empty($params['id'])) {
                throw new InvalidArgumentException('Empty id.');
            }
            $id = $params['id'];

            $url = $this->cache->getOrSet(['category_route', 'id' => $id], function () use ($id) {
                if (!$category = $this->repository->find($id)) {
                    return null;
                }
                return $this->getCategoryPath($category);
            }, null, new TagDependency(['tags' => ['categories']]));

            if (!$url) {
                throw new InvalidArgumentException('Undefined id.');
            }

            $url = $this->prefix . '/' . $url;
            unset($params['id']);
            if (!empty($params) && ($query = http_build_query($params)) !== '') {
                $url .= '?' . $query;
            }

            return $url;
        }
        return false;
    }

    private function getPathSlug(string $path): string
    {
        $chunks = explode('/', $path);
        return end($chunks);
    }

    private function getCategoryPath(Category $category): string
    {
        $chunks = ArrayHelper::getColumn($category->getParents()->andWhere(['>', 'depth', 0])->all(), 'slug');
        $chunks[] = $category->slug;
        return implode('/', $chunks);
    }
}

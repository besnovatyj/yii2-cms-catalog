<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\readModels;

use Besnovatyj\Catalog\entities\Brand;
use Besnovatyj\Catalog\entities\Category;
use Besnovatyj\Catalog\entities\product\Product;
use Besnovatyj\Contracts\tags\TaggedItem;
use Besnovatyj\Tags\entities\Tag;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

class ProductReadRepository
{
    private TreeQueryScope $treeScope;

    public function __construct()
    {
        $this->treeScope = new TreeQueryScope(Category::class);
    }

    public function getAll(): DataProviderInterface
    {
        $query = Product::find()->alias('p')->active('p')->with('mainPhoto', 'values');
        return $this->getProvider($query);
    }

    public function getAllByCategory(Category $category): DataProviderInterface
    {
        $query = Product::find()->alias('p')->active('p')->with('mainPhoto', 'category');
        $ids = $this->treeScope->descendantIds($category, andSelf: true);
        $query->joinWith(['categoryAssignments ca'], false);
        $query->andWhere(['or', ['p.category_id' => $ids], ['ca.category_id' => $ids]]);
        $query->groupBy('p.id');
        return $this->getProvider($query);
    }

    public function getAllByBrand(Brand $brand): DataProviderInterface
    {
        $query = Product::find()->alias('p')->active('p')->with('mainPhoto');
        $query->andWhere(['p.brand_id' => $brand->id]);
        return $this->getProvider($query);
    }

    public function getAllByTag(Tag $tag): DataProviderInterface
    {
        $query = Product::find()->alias('p')->active('p')->with('mainPhoto');
        $query->joinWith(['tagAssignments ta'], false);
        $query->andWhere(['ta.tag_id' => $tag->id]);
        $query->groupBy('p.id');
        return $this->getProvider($query);
    }

    public function getFeatured(int $limit): array
    {
        return Product::find()->active()->with('mainPhoto')->orderBy(['id' => SORT_DESC])->limit($limit)->all();
    }

    public function find($id): ?Product
    {
        return Product::find()->active()->andWhere(['id' => $id])->one();
    }

    /**
     * Из переданных id — товары, доступные анониму (для счётчиков страницы тега и облака).
     *
     * @param int[] $ids
     * @return int[]
     */
    public function visibleIds(array $ids): array
    {
        if ($ids === []) {
            return [];
        }
        return array_map('intval', Product::find()->alias('p')->active('p')->andWhere(['p.id' => $ids])->select('p.id')->column());
    }

    /**
     * Карточки товаров для страницы тега модуля Tags — только активные, в порядке `$ids`.
     *
     * @param int[] $ids
     * @return iterable<TaggedItem>
     */
    public function taggedItems(array $ids): iterable
    {
        if ($ids === []) {
            return;
        }

        /** @var Product[] $products */
        $products = Product::find()->alias('p')->active('p')->with('mainPhoto')->andWhere(['p.id' => $ids])->indexBy('id')->all();

        foreach ($ids as $id) {
            $product = $products[$id] ?? null;
            if ($product === null) {
                continue;
            }
            yield new TaggedItem(
                type: Product::tagType(),
                entityId: (int)$product->id,
                route: '/Catalog/product/item',
                params: ['id' => (int)$product->id],
                title: (string)$product->name,
                excerpt: $product->description_short !== null && $product->description_short !== '' ? (string)$product->description_short : null,
                // `created_at` — TIMESTAMP-колонка, контракт ждёт Unix-timestamp: только strtotime().
                date: $product->created_at === null ? null : (strtotime((string)$product->created_at) ?: null),
                image: $product->mainPhoto?->getThumbUrl('file', 'catalog_list'),
            );
        }
    }

    private function getProvider(ActiveQuery $query): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_ASC],
                'attributes' => [
                    'id' => [
                        'asc' => ['p.id' => SORT_ASC],
                        'desc' => ['p.id' => SORT_DESC],
                    ],
//                    'name' => [
//                        'asc' => ['p.name' => SORT_ASC],
//                        'desc' => ['p.name' => SORT_DESC],
//                    ],
//                    'price' => [
//                        'asc' => ['p.price_new' => SORT_ASC],
//                        'desc' => ['p.price_new' => SORT_DESC],
//                    ],
//                    'rating' => [
//                        'asc' => ['p.rating' => SORT_ASC],
//                        'desc' => ['p.rating' => SORT_DESC],
//                    ],
                ],
            ],
            'pagination' => [
                'pageSizeLimit' => [15, 100],
            ]
        ]);
    }

    public function findPrevProduct(Product $currentProduct): ?Product
    {
        $prev = $this->find($currentProduct->id - 1);
        if ($prev instanceof Product) {
            return $prev;
        }
        return null;
    }

    public function findNextProduct(Product $currentProduct): ?Product
    {
        $next = $this->find($currentProduct->id + 1);
        if (($next instanceof Product) && ($currentProduct->id !== $next->id)) {
            return $next;
        }
        return null;
    }
}

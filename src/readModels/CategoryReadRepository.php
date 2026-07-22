<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\readModels;

use Besnovatyj\Catalog\entities\Category;
use Besnovatyj\TreeManager\Manager\TreeQueryScope;
use yii\helpers\ArrayHelper;

class CategoryReadRepository
{
    private TreeQueryScope $treeScope;

    public function __construct()
    {
        $this->treeScope = new TreeQueryScope(Category::class);
    }

    /**
     * @return Category[]
     */
    public function getAll(): array
    {
        return Category::find()->orderBy('lft')->all();
    }

    public function find(int $id): ?Category
    {
        return Category::find()->andWhere(['id' => $id])->one();
    }

    public function findBySlug(string $slug): ?Category
    {
        return Category::find()->andWhere(['slug' => $slug])->one();
    }

    /**
     * ЧПУ-путь категории: слаги предков (без виртуального корня depth=0) и самого узла через «/».
     * Используется {@see \Besnovatyj\Catalog\urls\CategoryUrlRule} для разбора/генерации ЧПУ-адресов.
     */
    public function pathTo(Category $category): string
    {
        $nodes = $this->treeScope->parentsQuery($category, andSelf: true)
            ->andWhere(['>', 'depth', 0])
            ->all();

        return implode('/', ArrayHelper::getColumn($nodes, 'slug'));
    }

    public function getRoot(): Category
    {
        return Category::find()->andWhere(['depth' => 0])->one();
    }

    public function getTreeWithSubsOf(?Category $category = null): array
    {
        $query = Category::find()->orderBy('lft');
        if ($category) {
            $parents = $this->treeScope->parentsQuery($category)->all();
            $criteria = ['or', ['depth' => 1]];
            foreach (array_merge([$category], $parents) as $item) {
                $criteria[] = ['and', ['>', 'lft', $item->lft], ['<', 'rgt', $item->rgt], ['depth' => $item->depth + 1]];
            }
            $query->andWhere($criteria);
        } else {
            $query->andWhere(['depth' => 1]);
        }

        return $query->all();
    }

}

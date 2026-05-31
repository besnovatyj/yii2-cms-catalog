<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Catalog\services;

use Besnovatyj\Catalog\entities\product\Product;
use Besnovatyj\Catalog\entities\product\TagAssignment;
use Besnovatyj\Catalog\entities\Tag;
use Besnovatyj\Catalog\repositories\TagRepository;
use yii\db\Exception;
use yii\helpers\Inflector;

/**
 * Управление привязкой тегов к продукту
 */
class ProductTagService
{
    private TagRepository $tags;

    public function __construct(TagRepository $tags)
    {
        $this->tags = $tags;
    }

    /**
     * @param string[] $tagNames
     * @throws Exception
     */
    public function assign(Product $product, array $tagNames): void
    {
        foreach ($tagNames as $tagName) {
            $slug = Inflector::slug($tagName);

            $tag = $this->tags->findBySlug($slug);
            if (!$tag) {
                $tag = Tag::create($tagName, $slug);
                $this->tags->save($tag);
            }

            $exists = TagAssignment::find()
                ->andWhere(['product_id' => $product->id, 'tag_id' => $tag->id])
                ->exists();

            if ($exists) {
                continue;
            }

            $assignment = new TagAssignment();
            $assignment->product_id = $product->id;
            $assignment->tag_id = $tag->id;

            if (!$assignment->save()) {
                throw new Exception('Failed to save tag assignment.');
            }
        }
    }

    public function revoke(Product $product): void
    {
        TagAssignment::deleteAll(['product_id' => $product->id]);
    }
}

<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\services;

use Besnovatyj\Catalog\repositories\TagRepository;
use Besnovatyj\Catalog\entities\Tag;
use Besnovatyj\Catalog\forms\backend\TagForm;
use Throwable;
use yii\db\Exception;
use yii\db\StaleObjectException;

class TagManageService
{
    private TagRepository $tags;

    public function __construct(TagRepository $tags)
    {
        $this->tags = $tags;
    }

    /**
     * @throws Exception
     */
    public function create(TagForm $form): Tag
    {
        $tag = Tag::create(
            $form->name,
            $form->slug
        );
        $this->tags->save($tag);
        return $tag;
    }

    /**
     * @throws Exception
     */
    public function edit($id, TagForm $form): void
    {
        $tag = $this->tags->get($id);
        $tag->edit(
            $form->name,
            $form->slug
        );
        $this->tags->save($tag);
    }

    /**
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function remove($id): void
    {
        $tag = $this->tags->get($id);
        $this->tags->remove($tag);
    }
}

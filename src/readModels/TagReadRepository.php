<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\readModels;

use Besnovatyj\Catalog\entities\Tag;

class TagReadRepository
{
    public function find($id): ?Tag
    {
        return Tag::findOne($id);
    }
}

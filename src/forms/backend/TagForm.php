<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\forms\backend;

use Besnovatyj\Forms\BaseForm;
use Besnovatyj\Helpers\StringHelper;
use Besnovatyj\Validators\SlugValidator;
use Besnovatyj\Catalog\entities\Tag;


class TagForm extends BaseForm
{
    public string|null $name = null;
    public string|null $slug = null;

    private Tag|null $_tag = null;

    public function __construct(?Tag $tag = null, $config = [])
    {
        if ($tag) {
            $this->name = $tag->name;
            $this->slug = $tag->slug;
            $this->_tag = $tag;
        }
        parent::__construct($config);
    }

    /**
     * Генерирует slug, если не указан при создании
     */
    public function beforeValidate(): bool
    {
        $this->name = StringHelper::spaceReplace($this->name);
        $this->slug = $this->generateSlug($this->name, $this->slug);
        return parent::beforeValidate();
    }

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name', 'slug'], 'string', 'max' => 255],
            ['slug', SlugValidator::class],
            [['name', 'slug'], 'unique', 'targetClass' => Tag::class, 'filter' => $this->_tag ? ['<>', 'id', $this->_tag->id] : null]
        ];
    }
}

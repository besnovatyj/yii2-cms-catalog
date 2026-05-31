<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\forms\backend;

use Besnovatyj\Catalog\entities\Brand;
use Besnovatyj\Forms\CompositeForm;
use Besnovatyj\Helpers\StringHelper;
use Besnovatyj\Meta\MetaForm;
use Besnovatyj\Validators\SlugValidator;


/**
 * @property MetaForm $meta;
 */
class BrandForm extends CompositeForm
{
    public string|null $name = null;
    public string|null $slug = null;

    private Brand|null $_brand = null;

    public function __construct(?Brand $brand = null, $config = [])
    {
        if ($brand) {
            $this->name = $brand->name;
            $this->slug = $brand->slug;
            $this->meta = new MetaForm($brand->meta);
            $this->_brand = $brand;
        } else {
            $this->meta = new MetaForm();
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
            [['name', 'slug'], 'unique', 'targetClass' => Brand::class, 'filter' => $this->_brand ? ['<>', 'id', $this->_brand->id] : null]
        ];
    }

    public function internalForms(): array
    {
        return ['meta'];
    }
}

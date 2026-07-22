<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\forms\backend;

use Besnovatyj\Catalog\entities\Characteristic;
use Besnovatyj\Catalog\helpers\CharacteristicHelper;
use Besnovatyj\Forms\BaseForm;
use Besnovatyj\Helpers\StringHelper;
use Besnovatyj\Validators\SlugValidator;


/**
 * @property array $variants
 */
class CharacteristicForm extends BaseForm
{
    public string|null $name = null;
    public string|null $slug = null;
    public string|null $type = null;
    public int|null $required = null;
    public string|null $default = null;
    public string|null $textVariants = null;
    public int|null $sort = null;

    private Characteristic|null $_characteristic = null;

    public function __construct(?Characteristic $characteristic = null, $config = [])
    {
        if ($characteristic) {
            $this->name = $characteristic->name;
            $this->slug = $characteristic->slug;
            $this->type = $characteristic->type;
            $this->required = $characteristic->required;
            $this->default = $characteristic->default;
            $this->textVariants = implode(PHP_EOL, $characteristic->variants);
            $this->sort = $characteristic->sort;
            $this->_characteristic = $characteristic;
        } else {
            $this->sort = Characteristic::find()->max('sort') + 1;
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
            [['name', 'type', 'sort'], 'required'],
            [['required'], 'boolean'],
            [['default', 'slug'], 'string', 'max' => 255],
            [['textVariants'], 'string'],
            [['sort'], 'integer'],
            ['slug', SlugValidator::class],
            ['type', 'in', 'range' => [Characteristic::TYPE_STRING, Characteristic::TYPE_INTEGER, Characteristic::TYPE_FLOAT]],
            [['name', 'slug'], 'unique', 'targetClass' => Characteristic::class, 'filter' => $this->_characteristic ? ['<>', 'id', $this->_characteristic->id] : null]
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Название',
            'slug' => 'Slug (необязательно)',
            'type' => 'Тип',
            'required' => 'Обязательность заполнения',
            'default' => 'По умолчанию',
            'textVariants' => 'Варианты',
            'sort' => 'Сортировка',
        ];
    }

    public function typesList(): array
    {
        return CharacteristicHelper::typeList();
    }

    public function getVariants(): array
    {
        return preg_split('#\s+#', $this->textVariants ?: '');
    }
}

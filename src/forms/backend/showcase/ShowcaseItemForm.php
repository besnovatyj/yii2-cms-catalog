<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Catalog\forms\backend\showcase;

use Besnovatyj\Catalog\entities\product\Product;
use Besnovatyj\Catalog\entities\showcase\ShowcaseItem;
use yii\base\Model;

/**
 * Форма настройки элемента витрины: какое фото и из каких полей товара брать
 * заголовок и описание. Допустимые значения источников — ключи реестров
 * {@see Product::titleSources()} / {@see Product::descriptionSources()}.
 */
class ShowcaseItemForm extends Model
{
    public int|string|null $photo_index = null;
    public string|null $title_source = null;
    public string|null $description_source = null;

    public function __construct(?ShowcaseItem $item = null, $config = [])
    {
        if ($item) {
            $this->photo_index = $item->photo_index;
            $this->title_source = $item->title_source;
            $this->description_source = $item->description_source;
        }

        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['photo_index'], 'integer', 'min' => 0],
            [['photo_index'], 'default', 'value' => null],
            [['title_source'], 'default', 'value' => Product::TITLE_SOURCE_DEFAULT],
            [['title_source'], 'in', 'range' => array_keys(Product::titleSources())],
            [['description_source'], 'default', 'value' => Product::DESCRIPTION_SOURCE_DEFAULT],
            [['description_source'], 'in', 'range' => array_keys(Product::descriptionSources())],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'photo_index' => 'Индекс фото',
            'title_source' => 'Заголовок из поля',
            'description_source' => 'Описание из поля',
        ];
    }
}

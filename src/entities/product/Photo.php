<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\entities\product;

use Besnovatyj\Images\base\BaseImage;

/**
 * @property int $id
 * @property int $product_id
 * @property string $file
 * @property int $sort
 */
class Photo extends BaseImage
{
    /**
     * {@inheritdoc}
     */
    protected static function getParentAttribute(): string
    {
        return 'product_id'; // FK-атрибут в таблице изображений
    }

    /**
     * {@inheritdoc}
     */
    protected static function getStorageName(): string
    {
        return 'Catalog/Products'; // поддиректория в @static/origin/ и @static/cache/
    }

    protected static function getThumbProfiles(): array
    {
        return [
            'admin' => ['width' => 100, 'height' => 70], // список товаров в админке
            'thumb' => ['width' => 640, 'height' => 480], // страница товара в админке

            'slider' => ['width' => 1200, 'height' => 912], // слайдер на главной и на страницах товаров
            'slider_lazy' => ['width' => 1200, 'height' => 912, 'quality' => 10], // ленивая загрузка для 'slider'

            'by_category' => ['width' => 560, 'height' => 710, 'quality' => 90], // на странице 'by-category'
            'by_category_lazy' => ['width' => 560, 'height' => 710, 'quality' => 10], //  ленивая загрузка для 'by-category'

            'item' => ['width' => 1142, 'height' => 1188, 'quality' => 90], // на странице 'item'
            'item_lazy' => ['width' => 892, 'height' => 928, 'quality' => 10], //  ленивая загрузка для 'item'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{%catalog_photos}}';
    }

}

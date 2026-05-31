<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\helpers;

use Besnovatyj\Catalog\entities\product\Value;

class ValueHelper
{
    /**
     * Возвращает значение характеристики по её slug-у
     */
    public static function getValue(array $values, string $slug): string
    {
        $out = '';
        // Поиск в массиве значений характеристик по slug-у
        $resultArrayBySlug = array_filter($values, static function ($value) use ($slug) {
            return $value->slug === $slug;
        });
        // Т.к. slug уникален и результирующий массив должен содержать только один элемент, возвращаем его, независимо от значения ключа
        $value = current($resultArrayBySlug);
        if ($value instanceof Value) {
            $out = $value->value;
        }
        return $out;
    }

}

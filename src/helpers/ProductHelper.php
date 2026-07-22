<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\helpers;

use Exception;
use Besnovatyj\Catalog\entities\product\Product;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class ProductHelper
{
    public static function statusList(): array
    {
        return [
            Product::STATUS_DRAFT => 'Выкл',
            Product::STATUS_ACTIVE => 'Вкл',
        ];
    }

    /**
     * @throws Exception
     */
    public static function statusName($status): string
    {
        return ArrayHelper::getValue(self::statusList(), $status);
    }

    /**
     * @throws Exception
     */
    public static function statusLabel($status): string
    {
        $class = match ($status) {
            Product::STATUS_DRAFT => 'badge bg-default',
            Product::STATUS_ACTIVE => 'badge bg-success',
            default => 'badge badge-light',
        };

        return Html::tag('span', ArrayHelper::getValue(self::statusList(), $status), [
            'class' => $class,
        ]);
    }
}

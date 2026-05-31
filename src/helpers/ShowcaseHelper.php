<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Catalog\helpers;

use Besnovatyj\Catalog\entities\showcase\Showcase;
use Besnovatyj\Catalog\entities\showcase\ShowcaseItem;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * Хелпер витрин
 */
class ShowcaseHelper
{
    /**
     * @return array
     */
    public static function statusList(): array
    {
        return [
            Showcase::STATUS_DRAFT => 'Выкл',
            Showcase::STATUS_ACTIVE => 'Вкл',
        ];
    }

    /**
     * @param int $status
     * @return string
     * @throws \Exception
     */
    public static function statusLabel(int $status): string
    {
        $class = match ($status) {
            Showcase::STATUS_DRAFT => 'badge bg-default',
            Showcase::STATUS_ACTIVE => 'badge bg-success',
            default => 'badge badge-light',
        };

        return Html::tag('span', ArrayHelper::getValue(self::statusList(), $status), [
            'class' => $class,
        ]);
    }

    /**
     * @param int $status
     * @return string
     */
    public static function itemStatusLabel(int $status): string
    {
        $class = match ($status) {
            ShowcaseItem::STATUS_DRAFT => 'badge bg-secondary',
            ShowcaseItem::STATUS_ACTIVE => 'badge bg-success',
            default => 'badge badge-light',
        };

        $text = match ($status) {
            ShowcaseItem::STATUS_DRAFT => 'Выкл',
            ShowcaseItem::STATUS_ACTIVE => 'Вкл',
            default => '?',
        };

        return Html::tag('span', $text, ['class' => $class]);
    }
}

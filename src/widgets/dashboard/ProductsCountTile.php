<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Catalog\widgets\dashboard;

use Besnovatyj\Catalog\entities\product\Product;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Url;

/**
 * Плитка дашборда: общее число товаров каталога с разбивкой активные/черновики и ссылкой к списку.
 *
 * Рендерит только тело карточки — «каркас» (заголовок с иконкой) рисует модуль дашборда.
 */
class ProductsCountTile extends Widget
{
    public function run(): string
    {
        $total = (int)Product::find()->count();
        $active = (int)Product::find()->where(['status' => Product::STATUS_ACTIVE])->count();
        $draft = max(0, $total - $active);

        $number = Html::tag('span', (string)$total, ['class' => 'display-6 fw-bold lh-1'])
            . Html::tag('span', 'товаров', ['class' => 'text-muted ms-2']);

        $breakdown = Html::tag(
            'div',
            'Активных: ' . $active . ' · Черновиков: ' . $draft,
            ['class' => 'text-muted small mt-1']
        );

        $link = Html::a(
            '<i class="bi bi-box-seam me-1"></i>К товарам',
            Url::to(['/Catalog/backend/product/index']),
            ['class' => 'btn btn-sm btn-outline-primary mt-3']
        );

        return Html::tag('div', $number, ['class' => 'd-flex align-items-baseline']) . $breakdown . $link;
    }
}

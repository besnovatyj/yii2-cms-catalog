<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Catalog\readModels;

use Besnovatyj\Catalog\entities\product\Product;
use Besnovatyj\Catalog\entities\showcase\Showcase;
use Besnovatyj\Catalog\entities\showcase\ShowcaseItem;

/**
 * ReadRepository для получения данных витрины на фронтенде
 */
class ShowcaseReadRepository
{
    /**
     * Получить активную витрину по коду с загруженными элементами и товарами
     *
     * @param string $code
     * @return Showcase|null
     */
    public function findByCode(string $code): ?Showcase
    {
        return Showcase::find()
            ->andWhere([
                'code' => $code,
                'status' => Showcase::STATUS_ACTIVE,
            ])
            ->one();
    }

    /**
     * Получить активные элементы витрины с товарами, фотографиями и характеристиками
     *
     * @param string $code
     * @return ShowcaseItem[]
     */
    public function getItemsByCode(string $code): array
    {
        $showcase = $this->findByCode($code);
        if (!$showcase) {
            return [];
        }

        return ShowcaseItem::find()
            ->andWhere([
                'showcase_id' => $showcase->id,
                'status' => ShowcaseItem::STATUS_ACTIVE,
            ])
            ->with([
                'product' => static function ($query) {
                    /** @var \yii\db\ActiveQuery $query */
                    $query->andWhere(['status' => Product::STATUS_ACTIVE]);
                },
                'product.photos',
                'product.values',
                'product.mainPhoto',
            ])
            ->orderBy(['sort' => SORT_ASC])
            ->all();
    }
}

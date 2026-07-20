<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Catalog\widgets\showcase;

use Besnovatyj\Catalog\entities\product\Product;
use Besnovatyj\Catalog\entities\showcase\ShowcaseItem;
use Besnovatyj\Catalog\readModels\ShowcaseReadRepository;
use yii\base\Widget;

/**
 * Data-only виджет витрины товаров.
 *
 * В отличие от {@see ShowcaseWidget}, ничего не рендерит, а лишь предоставляет
 * данные витрины для отрисовки силами темы. Разметка (слайдер и т.п.) остаётся
 * ответственностью темы, пакет не знает про конкретную вёрстку.
 *
 * Использование в теме:
 * ```php
 * <?php $widget = ShowcaseProductsArrayWidget::begin(['code' => 'homepage-slider']); ?>
 * <?php foreach ($widget->products as $product): ?>
 *     ...
 * <?php endforeach; ?>
 * ```
 */
class ShowcaseProductsArrayWidget extends Widget
{
    /**
     * Код витрины (из таблицы catalog_showcases.code)
     */
    public string $code = '';

    /**
     * Активные элементы витрины с уже загруженным (не null) товаром.
     *
     * @var ShowcaseItem[]
     */
    public array $items = [];

    /**
     * Товары элементов витрины в том же порядке, что и {@see $items}.
     * Удобно для темы, которой нужны только сами товары.
     *
     * @var Product[]
     */
    public array $products = [];

    public ShowcaseReadRepository $repository;

    public function __construct(ShowcaseReadRepository $repository, $config = [])
    {
        // Присваиваем зависимость ДО parent::__construct(): BaseObject внутри
        // конструктора применяет $config и сразу вызывает init(), где repository
        // уже должен быть доступен.
        $this->repository = $repository;
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        if ($this->code === '') {
            return;
        }

        // Фильтруем элементы, у которых товар удалён или деактивирован
        // (поведение совпадает с ShowcaseWidget).
        $this->items = array_values(array_filter(
            $this->repository->getItemsByCode($this->code),
            static fn(ShowcaseItem $item): bool => $item->product !== null,
        ));

        $this->products = array_map(
            static fn(ShowcaseItem $item): Product => $item->product,
            $this->items,
        );
    }
}

<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Catalog\widgets\showcase;

use Besnovatyj\Catalog\readModels\ShowcaseReadRepository;
use yii\base\Widget;

/**
 * Виджет витрины товаров
 *
 * Использование в теме:
 * ```php
 * <?= ShowcaseWidget::widget(['code' => 'homepage-slider']) ?>
 * ```
 */
class ShowcaseWidget extends Widget
{
    /**
     * Код витрины (из таблицы catalog_showcases.code)
     */
    public string $code = '';

    /**
     * Путь к кастомному view-файлу (опционально)
     */
    public ?string $viewFile = null;

    public ShowcaseReadRepository $repository;

    public function __construct(ShowcaseReadRepository $repository, $config = [])
    {
        parent::__construct($config);
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function run(): string
    {
        if (empty($this->code)) {
            return '';
        }

        $items = $this->repository->getItemsByCode($this->code);

        // Фильтруем элементы, у которых товар удалён или деактивирован
        $items = array_filter($items, static function ($item) {
            return $item->product !== null;
        });

        if (empty($items)) {
            return '';
        }

        $viewFile = $this->viewFile ?: 'default';

        return $this->render($viewFile, [
            'items' => array_values($items),
        ]);
    }
}

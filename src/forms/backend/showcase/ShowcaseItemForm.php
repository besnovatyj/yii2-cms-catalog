<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Catalog\forms\backend\showcase;

use Besnovatyj\Catalog\entities\showcase\ShowcaseItem;
use yii\base\Model;

/**
 * Форма настройки элемента витрины
 */
class ShowcaseItemForm extends Model
{
    public int|string|null $photo_index = null;
    public array|null $display_characteristics = null;
    public string|null $custom_title = null;

    public function __construct(?ShowcaseItem $item = null, $config = [])
    {
        if ($item) {
            $this->photo_index = $item->photo_index;
            $this->display_characteristics = $item->getDisplayCharacteristicSlugs();
            $this->custom_title = $item->custom_title;
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
            [['custom_title'], 'string', 'max' => 255],
            [['display_characteristics'], 'each', 'rule' => ['string']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'photo_index' => 'Индекс фото',
            'display_characteristics' => 'Характеристики для отображения',
            'custom_title' => 'Кастомный заголовок',
        ];
    }
}

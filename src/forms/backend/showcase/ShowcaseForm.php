<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

declare(strict_types=1);

namespace Besnovatyj\Catalog\forms\backend\showcase;

use Besnovatyj\Catalog\entities\showcase\Showcase;
use yii\base\Model;

/**
 * Форма создания/редактирования витрины
 */
class ShowcaseForm extends Model
{
    public string|null $code = null;
    public string|null $name = null;
    public int $sort = 0;

    private ?Showcase $_showcase = null;

    public function __construct(?Showcase $showcase = null, $config = [])
    {
        if ($showcase) {
            $this->code = $showcase->code;
            $this->name = $showcase->name;
            $this->sort = $showcase->sort;
            $this->_showcase = $showcase;
        }

        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['code', 'name'], 'required'],
            [['code', 'name'], 'string', 'max' => 255],
            [['code'], 'match', 'pattern' => '/^[a-z0-9\-_]+$/', 'message' => 'Код может содержать только латинские буквы, цифры, дефис и подчёркивание.'],
            [['code'], 'unique', 'targetClass' => Showcase::class, 'filter' => $this->_showcase ? ['<>', 'id', $this->_showcase->id] : null],
            [['sort'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'code' => 'Код',
            'name' => 'Название',
            'sort' => 'Сортировка',
        ];
    }
}

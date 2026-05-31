<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\forms\backend\product;

use Besnovatyj\Catalog\entities\Characteristic;
use Besnovatyj\Catalog\entities\product\Value;
use Besnovatyj\Forms\BaseForm;

class ValueForm extends BaseForm
{
    public string|null $value = null;

    private Characteristic $_characteristic;

    public function __construct(Characteristic $characteristic, ?Value $value = null, $config = [])
    {
        if ($value) {
            $this->value = $value->value;
        }
        $this->_characteristic = $characteristic;
        parent::__construct($config);
    }

    public function rules(): array
    {
        return array_filter([
            $this->_characteristic->required ? ['value', 'required'] : false,
            $this->_characteristic->isString() ? ['value', 'string', 'max' => 255] : false,
            $this->_characteristic->isInteger() ? ['value', 'integer'] : false,
            $this->_characteristic->isFloat() ? ['value', 'number'] : false,
            ['value', 'safe'],
        ]);
    }

    public function attributeLabels(): array
    {
        return [
            'value' => $this->_characteristic->name,
        ];
    }

    public function variantsList(): array
    {
        return $this->_characteristic->variants ? array_combine($this->_characteristic->variants, $this->_characteristic->variants) : [];
    }

    public function getCharacteristicId(): int
    {
        return $this->_characteristic->id;
    }

    public function getCharacteristicSlug(): string
    {
        return $this->_characteristic->slug;
    }
}

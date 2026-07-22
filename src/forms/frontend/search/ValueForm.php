<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\forms\frontend\Search;

use Besnovatyj\Catalog\entities\Characteristic;
use Besnovatyj\Catalog\entities\product\Value;
use Besnovatyj\Forms\BaseForm;
use yii\base\Model;

/**
 * @property integer $id
 */
class ValueForm extends BaseForm
{
    public string|null $from = null;
    public string|null $to = null;
    public string|null $equal = null;

    private Characteristic $_characteristic;

    public function __construct(Characteristic $characteristic, $config = [])
    {
        $this->_characteristic = $characteristic;
        parent::__construct($config);
    }

    /** TODO - разобраться с типами свойств класса и с валидаторами */
    public function rules(): array
    {
        return array_filter([
            $this->_characteristic->isString() ? ['equal', 'string'] : false,
            $this->_characteristic->isInteger() || $this->_characteristic->isFloat() ? [['from', 'to'], 'integer'] : false
        ]);
    }

    public function isFilled(): bool
    {
        return !empty($this->from) || !empty($this->to) || !empty($this->equal);
    }

    public function variantsList(): array
    {
        return $this->_characteristic->variants ? array_combine($this->_characteristic->variants, $this->_characteristic->variants) : [];
    }

    public function getCharacteristicName(): string
    {
        return $this->_characteristic->name;
    }

    public function getId(): int
    {
        return $this->_characteristic->id;
    }

    public function formName(): string
    {
        return 'v';
    }
}

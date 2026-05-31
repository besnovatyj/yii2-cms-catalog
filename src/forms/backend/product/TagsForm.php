<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\forms\backend\product;

use Besnovatyj\Catalog\entities\product\Product;
use Besnovatyj\Forms\BaseForm;
use Besnovatyj\Helpers\StringHelper;
use yii\helpers\ArrayHelper;

class TagsForm extends BaseForm
{
    public array $newTagsNames = [];

    // TODO смотри лучше  как в блоге сделано
    public function __construct(?Product $product = null, $config = [])
    {
        if ($product) {
            $this->newTagsNames = ArrayHelper::map($product->tags, 'id', 'name');
        }
        parent::__construct($config);
    }

    public function beforeValidate(): bool
    {
        $this->newTagsNames = array_filter(array_map(static function ($tagName) {
            return StringHelper::spaceReplace($tagName);
        }, array_values($this->newTagsNames)
        ));

        return parent::beforeValidate();
    }

    public function rules(): array
    {
        return [
            ['newTagsNames', 'each', 'rule' => ['string', 'length' => [0, 255]]],
        ];
    }

}

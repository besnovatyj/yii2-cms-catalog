<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\forms\backend\product;

use Besnovatyj\Catalog\entities\Brand;
use Besnovatyj\Catalog\entities\Characteristic;
use Besnovatyj\Catalog\entities\product\Product;
use Besnovatyj\Forms\CompositeForm;
use Besnovatyj\Meta\MetaForm;
use yii\helpers\ArrayHelper;

/**
 * @property MetaForm $metaForm
 * @property CategoriesForm $categoriesForm
 * @property TagsForm $tagsForm
 * @property ValueForm[] $valueForms
 */
class ProductForm extends CompositeForm
{
    public int|null $brandId = null;
    public string|null $code = null;
    public string|null $name = null;
    public string|null $name_short = null;
    public string|null $description = null;
    public string|null $description_short = null;
    public string|null $weight = null;

    private ?Product $_product = null;

    public function __construct(?Product $product = null, $config = [])
    {
        if ($product) {
            $this->brandId = $product->brand_id;
            $this->code = $product->code;
            $this->name = $product->name;
            $this->name_short = $product->name_short;
            $this->description = $product->description;
            $this->description_short = $product->description_short;
            $this->weight = $product->weight;
            $this->metaForm = new MetaForm($product->meta);
            $this->categoriesForm = new CategoriesForm($product);
            $this->tagsForm = new TagsForm($product);
            $this->valueForms = array_map(static function (Characteristic $characteristic) use ($product) {
                return new ValueForm($characteristic, $product->getValue($characteristic->id));
            }, Characteristic::find()->orderBy('sort')->all());
            $this->_product = $product;
        } else {
            $this->metaForm = new MetaForm();
            $this->categoriesForm = new CategoriesForm();
            $this->tagsForm = new TagsForm();
            $this->valueForms = array_map(static function (Characteristic $characteristic) {
                return new ValueForm($characteristic);
            }, Characteristic::find()->orderBy('sort')->all());
        }

        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['brandId', 'name'], 'required'],
            [['brandId'], 'integer'],
            [['code', 'name', 'name_short', 'weight'], 'string', 'max' => 255],
            [['code'], 'unique', 'targetClass' => Product::class, 'filter' => $this->_product ? ['<>', 'id', $this->_product->id] : null],
            [['description', 'description_short',], 'string'],
        ];
    }

    public function brandsList(): array
    {
        return ArrayHelper::map(Brand::find()->orderBy('name')->asArray()->all(), 'id', 'name');
    }

    protected function internalForms(): array
    {
        return ['metaForm', 'categoriesForm', 'tagsForm', 'valueForms'];
    }
}

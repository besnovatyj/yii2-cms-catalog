<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\forms\backend\search;

use Besnovatyj\Catalog\helpers\ProductHelper;
use Besnovatyj\Catalog\entities\Category;
use Besnovatyj\Catalog\entities\product\Product;
use Besnovatyj\Forms\BaseForm;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class ProductSearch extends BaseForm
{
    public int|null $id = null;
    public string|null $code = null;
    public string|null $name = null;
    public int|null $category_id = null;
    public int|null $brand_id = null;
    public int|null $quantity = null;
    public int|null $status = null;

    public function rules(): array
    {
        return [
            [['id', 'category_id', 'brand_id', 'status', 'quantity'], 'integer'],
            [['code', 'name'], 'string'],
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Product::find()->with('mainPhoto', 'category');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'status' => $this->status,
            'quantity' => $this->quantity,
        ]);

        $query
            ->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }

    public function categoriesList(): array
    {
        return ArrayHelper::map(Category::find()->andWhere(['>', 'depth', 0])->orderBy('lft')->asArray()->all(), 'id', function (array $category) {
            return ($category['depth'] > 1 ? str_repeat('-- ', $category['depth'] - 1) . ' ' : '') . $category['name'];
        });
    }

    public function statusList(): array
    {
        return ProductHelper::statusList();
    }
}

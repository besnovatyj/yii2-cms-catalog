<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\forms\backend\search;

use Besnovatyj\Catalog\entities\Brand;
use Besnovatyj\Forms\BaseForm;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class BrandSearch extends BaseForm
{
    public int|null $id = null;
    public string|null $name = null;
    public string|null $slug = null;

    public function rules(): array
    {
        return [
            [['id'], 'integer'],
            [['name', 'slug'], 'string'],
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Brand::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['name' => SORT_ASC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'slug', $this->slug]);

        return $dataProvider;
    }
}

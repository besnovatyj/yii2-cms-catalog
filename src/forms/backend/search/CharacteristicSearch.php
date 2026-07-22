<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\forms\backend\search;

use Besnovatyj\Catalog\helpers\CharacteristicHelper;
use Besnovatyj\Catalog\entities\Characteristic;
use Besnovatyj\Forms\BaseForm;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class CharacteristicSearch extends BaseForm
{
    public int|null $id = null;
    public int|null $required = null;
    public string|null $name = null;
    public string|null $type = null;

    public function rules(): array
    {
        return [
            [['id', 'required'], 'integer'],
            [['type', 'name'], 'string'],
        ];
    }

    /**
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Characteristic::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['sort' => SORT_ASC]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'type' => $this->type,
            'required' => $this->required,
        ]);

        $query
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }

    public function typesList(): array
    {
        return CharacteristicHelper::typeList();
    }

    public function requiredList(): array
    {
        return [
            1 => Yii::$app->formatter->asBoolean(true),
            0 => Yii::$app->formatter->asBoolean(false),
        ];
    }
}

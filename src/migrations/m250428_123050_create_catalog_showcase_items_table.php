<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use yii\base\NotSupportedException;
use yii\db\Exception;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m250428_123050_create_catalog_showcase_items_table extends BaseMigration
{
    public const string TABLE_NAME = '{{%catalog_showcase_items}}';

    /**
     * @throws NotSupportedException
     */
    public function safeUp(): void
    {
        parent::safeUp();

        if ($this->existTable(static::TABLE_NAME)) {
            return;
        }

        $this->createTable(static::TABLE_NAME, [
            'id' => $this->primaryKey(),
            'showcase_id' => $this->integer(10)->notNull()
                ->comment('Идентификатор витрины'),
            'product_id' => $this->integer(10)->notNull()
                ->comment('Идентификатор товара'),
            'photo_index' => $this->integer(10)->null()
                ->comment('Индекс фото товара (null = главное фото)'),
            'title_source' => $this->string(32)->null()
                ->comment('Ключ поля-заголовка товара (name|name_short); null = дефолт'),
            'description_source' => $this->string(32)->null()
                ->comment('Ключ поля-описания товара (spec|description|...); null = дефолт'),
            'sort' => $this->integer(10)->notNull()->defaultValue(0)
                ->comment('Сортировка'),
            'status' => $this->smallInteger(5)->notNull()->defaultValue(1)
                ->comment('Статус элемента'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Элементы витрины товаров');

        $this->createIndexes(static::TABLE_NAME, 'showcase_id');
        $this->createIndexes(static::TABLE_NAME, 'product_id');

        parent::safeUp();
    }

}

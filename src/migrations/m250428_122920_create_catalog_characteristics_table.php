<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\migrations;

use common\components\migration\BaseMigration;
use yii\base\NotSupportedException;
use yii\db\Exception;

/**
 * Handles the creation of table `catalog_characteristics`.
 * 'm<YYMMDD_HHMMSS>_<Name>'
 */
class m250428_122920_create_catalog_characteristics_table extends BaseMigration
{
    public const string TABLE_NAME = '{{%catalog_characteristics}}';

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
            'name' => $this->string(255)->notNull()
                ->comment('Название характеристики'),
            /**
             * Slug вводится для облегчения доступа к конкретной характеристике из View.
             * С его помощью у разных товаров можно будет запросить конкретную хар-ку, независимо от characteristic.id
             * В таблице значений уникальна пара "value.product_id - value.slug", здесь уникальное значение 'characteristic.slug'.
             */
            'slug' => $this->string(255)->notNull()
                ->comment('Уникальный человекопонятный идентификатор характеристики'),
            'type' => $this->string(16)->notNull()
                ->comment('Тип характеристики'),
            'required' => $this->tinyInteger(1)->notNull()
                ->comment('Обязательность заполнения'),
            'default' => $this->string(255)->null()
                ->comment('Значение по умолчанию'),
            'variants_json' => $this->text()->notNull()
                ->comment('Варианты для выбора'),
            'sort' => $this->integer(10)->notNull()
                ->comment('Сортировка характеристик'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Характеристики товаров EAV');

        $this->createIndexes(static::TABLE_NAME, 'slug', false, true);

        parent::safeUp();
    }

}

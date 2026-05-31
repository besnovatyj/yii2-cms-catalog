<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\migrations;

use common\components\migration\BaseMigration;
use yii\base\NotSupportedException;
use yii\db\Exception;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m250428_122950_create_catalog_values_table extends BaseMigration
{
    public const string TABLE_NAME = '{{%catalog_values}}';

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
            'product_id' => $this->integer(10)->notNull()
                ->comment('Идентификатор товара'),
            'characteristic_id' => $this->integer(10)->notNull()
                ->comment('Идентификатор характеристики'),
            'value' => $this->text()->null()
                ->comment('Значение характеристики'),
            /**
             * Slug вводится для облегчения доступа к конкретной характеристике из View.
             * С его помощью у разных товаров можно будет запросить конкретную хар-ку, независимо от characteristic.id
             * В таблице характеристик значение 'characteristic.slug' уникальное, здесь уникальна пара "value.product_id - value.slug".
             */
            'slug' => $this->string(255)->notNull()
                ->comment('Уникальный человекопонятный идентификатор характеристики'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Значение хар-ки EAV');

        $this->createIndexes(static::TABLE_NAME, ['product_id', 'characteristic_id'], true);
        $this->createIndexes(static::TABLE_NAME, ['product_id', 'slug'], false, true);
        $this->createIndexes(static::TABLE_NAME, 'product_id');
        $this->createIndexes(static::TABLE_NAME, 'slug');
        $this->createIndexes(static::TABLE_NAME, 'characteristic_id');

        parent::safeUp();
    }

}

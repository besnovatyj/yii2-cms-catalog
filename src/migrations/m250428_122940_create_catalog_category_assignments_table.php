<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use yii\base\NotSupportedException;
use yii\db\Exception;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m250428_122940_create_catalog_category_assignments_table extends BaseMigration
{
    public const string TABLE_NAME = '{{%catalog_category_assignments}}';

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
            'category_id' => $this->integer(10)->notNull()
                ->comment('Идентификатор категории товара '),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Связь категорий с товарами');

        $this->createIndexes(static::TABLE_NAME, ['product_id', 'category_id'], true);
        $this->createIndexes(static::TABLE_NAME, 'product_id');
        $this->createIndexes(static::TABLE_NAME, 'category_id');

        parent::safeUp();
    }

}

<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use yii\base\NotSupportedException;
use yii\db\Exception;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m250428_123020_create_catalog_related_assignments_table extends BaseMigration
{
    public const string TABLE_NAME = '{{%catalog_related_assignments}}';

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
            'related_id' => $this->integer(10)->notNull()
                ->comment('Идентификатор связанного товара'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Связанные товары');

        $this->createIndexes(static::TABLE_NAME, 'product_id');
        $this->createIndexes(static::TABLE_NAME, 'related_id');
        $this->createIndexes(static::TABLE_NAME, ['product_id', 'related_id'], true);

        parent::safeUp();
    }

}

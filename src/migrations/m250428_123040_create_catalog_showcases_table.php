<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use yii\base\NotSupportedException;
use yii\db\Exception;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m250428_123040_create_catalog_showcases_table extends BaseMigration
{
    public const string TABLE_NAME = '{{%catalog_showcases}}';

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
            'code' => $this->string(255)->notNull()
                ->comment('Уникальный код витрины для вызова из темы'),
            'name' => $this->string(255)->notNull()
                ->comment('Название витрины'),
            'status' => $this->smallInteger(5)->notNull()->defaultValue(0)
                ->comment('Статус витрины'),
            'sort' => $this->integer(10)->notNull()->defaultValue(0)
                ->comment('Сортировка'),
            'category_id' => $this->integer()->null()
                ->comment('Категория, страницу которой показывает эта витрина (null = свободная витрина)'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Витрины товаров');

        $this->createIndexes(static::TABLE_NAME, 'code', false, true);
        $this->createIndexes(static::TABLE_NAME, 'status');
        $this->createIndexes(static::TABLE_NAME, 'category_id');

        parent::safeUp();
    }

}

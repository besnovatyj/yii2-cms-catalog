<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use yii\base\NotSupportedException;
use yii\db\Exception;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m250428_123000_create_catalog_photos_table extends BaseMigration
{
    public const string TABLE_NAME = '{{%catalog_photos}}';

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
            'product_id' => $this->integer(10)->notNull()
                ->comment('Идентификатор продукта'),
            'file' => $this->string(255)->notNull()
                ->comment('Путь к файлу фотографии'),
            'sort' => $this->integer(10)->notNull()
                ->comment('Сортировка фотографий'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Фото товаров');

        $this->createIndexes(static::TABLE_NAME, 'product_id');

        parent::safeUp();
    }

}

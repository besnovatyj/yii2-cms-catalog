<?php


/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\migrations;

use common\components\migration\BaseMigration;
use yii\base\NotSupportedException;
use yii\db\Exception;

/** 'm<YYMMDD_HHMMSS>_<Name>' */
class m250428_122930_create_catalog_products_table extends BaseMigration
{
    public const string TABLE_NAME = '{{%catalog_products}}';

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
            'category_id' => $this->integer(10)->notNull()
                ->comment('Идентификатор категории товара'),
            'brand_id' => $this->integer(10)->notNull()
                ->comment('Идентификатор бренда товара'),
            'created_at' => $this->timestamp()->null()->defaultExpression('CURRENT_TIMESTAMP')
                ->comment('Дата и время создания товара'),
            'code' => $this->string(255)->null()
                ->comment('Код товара'),
            'name' => $this->string(255)->notNull()
                ->comment('Название товара'),
            'name_short' => $this->string(255)->null()
                ->comment('Название товара (short)'),
            'description' => $this->text()->null()
                ->comment('Описание товара'),
            'description_short' => $this->text()->null()
                ->comment('Описание товара (short)'),
            'meta_json' => $this->text()->null()
                ->comment('JSON meta'),
            'main_photo_id' => $this->integer(10)->null()
                ->comment('Идентификатор главной фотографии товара'),
            'status' => $this->smallInteger(5)->notNull()
                ->comment('Статус товара'),
            'weight' => $this->string(255)->null()
                ->comment('Масса единицы товара'),
        ], $this->tableOptions);
        $this->addCommentOnTable(static::TABLE_NAME, 'Товары');

        $this->createIndexes(static::TABLE_NAME, 'code', false, true);
        $this->createIndexes(static::TABLE_NAME, 'category_id');
        $this->createIndexes(static::TABLE_NAME, 'brand_id');
        $this->createIndexes(static::TABLE_NAME, 'main_photo_id');

        parent::safeUp();

    }

}

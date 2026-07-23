<?php

/*
 * Copyright (c) 2026 Besnovatyj. Licensed under the MIT License.
 */

namespace Besnovatyj\Catalog\migrations;

use Besnovatyj\Kernel\migration\BaseMigration;
use Yii;
use yii\db\Exception;

class m250428_123100_create_catalog_foreign_key_constraints extends BaseMigration
{

    /**
     * @throws Exception
     */
    public function safeUp(): void
    {
        parent::safeUp();

        Yii::$app->getDb()->createCommand("SET foreign_key_checks = 0")->execute();

        // catalog_products
        $this->createFKs(
            m250428_122930_create_catalog_products_table::TABLE_NAME,
            'category_id',
            m250428_122910_create_catalog_categories_table::TABLE_NAME,
            'id',
        );
        $this->createFKs(
            m250428_122930_create_catalog_products_table::TABLE_NAME,
            'brand_id', m250428_122900_create_catalog_brands_table::TABLE_NAME,
            'id',
        );
        $this->createFKs(
            m250428_122930_create_catalog_products_table::TABLE_NAME,
            'main_photo_id',
            m250428_123000_create_catalog_photos_table::TABLE_NAME,
            'id',
            'SET NULL',
            'RESTRICT',
        );

        // category_assignments
        $this->createFKs(
            m250428_122940_create_catalog_category_assignments_table::TABLE_NAME,
            'product_id',
            m250428_122930_create_catalog_products_table::TABLE_NAME,
            'id',
            'CASCADE',
            'RESTRICT',
        );
        $this->createFKs(
            m250428_122940_create_catalog_category_assignments_table::TABLE_NAME,
            'category_id',
            m250428_122910_create_catalog_categories_table::TABLE_NAME,
            'id',
            'CASCADE',
            'RESTRICT',
        );

        // catalog_photos
        $this->createFKs(
            m250428_123000_create_catalog_photos_table::TABLE_NAME,
            'product_id',
            m250428_122930_create_catalog_products_table::TABLE_NAME,
            'id',
            'CASCADE',
            'RESTRICT',
        );

        // catalog_tag_assignments
        $this->createFKs(
            m250428_123010_create_catalog_tag_assignments_table::TABLE_NAME,
            'product_id',
            m250428_122930_create_catalog_products_table::TABLE_NAME,
            'id',
            'CASCADE',
            'RESTRICT',
        );
        $this->createFKs(
            m250428_123010_create_catalog_tag_assignments_table::TABLE_NAME,
            'tag_id',
            m250428_122850_create_catalog_tags_table::TABLE_NAME,
            'id',
            'CASCADE',
            'RESTRICT',
        );

        // catalog_related_assignments
        $this->createFKs(
            m250428_123020_create_catalog_related_assignments_table::TABLE_NAME,
            'product_id',
            m250428_122930_create_catalog_products_table::TABLE_NAME,
            'id',
            'CASCADE',
            'RESTRICT',
        );
        $this->createFKs(
            m250428_123020_create_catalog_related_assignments_table::TABLE_NAME,
            'related_id',
            m250428_122930_create_catalog_products_table::TABLE_NAME,
            'id',
            'CASCADE',
            'RESTRICT',
        );

        // catalog_showcase_items
        $this->createFKs(
            m250428_123050_create_catalog_showcase_items_table::TABLE_NAME,
            'showcase_id',
            m250428_123040_create_catalog_showcases_table::TABLE_NAME,
            'id',
            'CASCADE',
            'RESTRICT',
        );
        $this->createFKs(
            m250428_123050_create_catalog_showcase_items_table::TABLE_NAME,
            'product_id',
            m250428_122930_create_catalog_products_table::TABLE_NAME,
            'id',
            'CASCADE',
            'RESTRICT',
        );

        // catalog_showcases: необязательная привязка витрины к категории
        $this->createFKs(
            m250428_123040_create_catalog_showcases_table::TABLE_NAME,
            'category_id',
            m250428_122910_create_catalog_categories_table::TABLE_NAME,
            'id',
            'SET NULL',
            'RESTRICT',
        );

        Yii::$app->db->createCommand('SET foreign_key_checks = 1')->execute();

    }

    public function safeDown(): void
    {
        // Отменяем действия по умолчанию,
        // так как \Besnovatyj\Kernel\migration\BaseMigration::safeDown() вызывает static::TABLE_NAME,
        // которого в данной миграции не существует.
        // Так же, \Besnovatyj\Kernel\migration\BaseMigration::safeDown() при удалении таблиц сам удалит у них все индексы и внешние ключи.

        // parent::safeDown();
    }

}

<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%showcase}}`.
 */
class m230306_090708_create_showcase_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('showcase', [
            'id' => $this->primaryKey(),
            'count' => $this->integer()->notNull(),
            'product_id' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk_product_id',
            'showcase',
            'product_id',
            'products',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_product_id', 'showcase');
        $this->dropTable('showcase');
    }
}

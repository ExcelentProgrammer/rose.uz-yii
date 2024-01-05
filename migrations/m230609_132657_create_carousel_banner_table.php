<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%carousel_banner}}`.
 */
class m230609_132657_create_carousel_banner_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%carousel_banner}}', [
            'id' => $this->primaryKey(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%carousel_banner}}');
    }
}

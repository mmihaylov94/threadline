<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserFavorites extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'BIGINT',
            ],
            'thread_id' => [
                'type' => 'BIGINT',
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addUniqueKey(['user_id', 'thread_id']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('thread_id', 'threads', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_favorites', true);
    }

    public function down()
    {
        $this->forge->dropTable('user_favorites', true);
    }
}

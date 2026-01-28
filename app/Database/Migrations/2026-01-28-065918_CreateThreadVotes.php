<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateThreadVotes extends Migration
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
            // +1 (upvote) or -1 (downvote)
            'value' => [
                'type' => 'SMALLINT',
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_id', 'thread_id']);
        $this->forge->addKey('thread_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('thread_id', 'threads', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('thread_votes', true);

        // PostgreSQL-compatible check constraint for vote value
        $db = \Config\Database::connect();
        $db->query("ALTER TABLE thread_votes ADD CONSTRAINT thread_votes_value_check CHECK (value IN (-1, 1))");
    }

    public function down()
    {
        $this->forge->dropTable('thread_votes', true);
    }
}

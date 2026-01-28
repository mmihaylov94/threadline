<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateModerationQueues extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'auto_increment' => true,
            ],
            'report_id' => [
                'type' => 'BIGINT',
            ],
            'priority' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'normal',
            ],
            'assigned_to' => [
                'type' => 'BIGINT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'pending',
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
        $this->forge->addKey('report_id');
        $this->forge->addKey('assigned_to');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('report_id', 'content_reports', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('assigned_to', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('moderation_queues', true);

        // Add CHECK constraints for PostgreSQL compatibility
        $db = \Config\Database::connect();
        $db->query("ALTER TABLE moderation_queues ADD CONSTRAINT ck_moderation_queues_priority CHECK (priority IN ('low', 'normal', 'high', 'urgent'))");
        $db->query("ALTER TABLE moderation_queues ADD CONSTRAINT ck_moderation_queues_status CHECK (status IN ('pending', 'in_progress', 'completed', 'cancelled'))");
    }

    public function down()
    {
        $this->forge->dropTable('moderation_queues', true);
    }
}

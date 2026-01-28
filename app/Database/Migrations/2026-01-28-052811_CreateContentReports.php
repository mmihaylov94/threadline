<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContentReports extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'auto_increment' => true,
            ],
            'reporter_id' => [
                'type' => 'BIGINT',
            ],
            'content_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'content_id' => [
                'type' => 'BIGINT',
            ],
            'guideline_violations' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'additional_notes' => [
                'type' => 'TEXT',
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
        $this->forge->addKey(['content_type', 'content_id']);
        $this->forge->addKey('status');
        $this->forge->addForeignKey('reporter_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('content_reports', true);
        
        // Add CHECK constraints for PostgreSQL compatibility
        $db = \Config\Database::connect();
        $db->query("ALTER TABLE content_reports ADD CONSTRAINT content_reports_content_type_check CHECK (content_type IN ('thread', 'post'))");
        $db->query("ALTER TABLE content_reports ADD CONSTRAINT content_reports_status_check CHECK (status IN ('pending', 'reviewed', 'resolved', 'dismissed'))");
    }

    public function down()
    {
        $this->forge->dropTable('content_reports', true);
    }
}

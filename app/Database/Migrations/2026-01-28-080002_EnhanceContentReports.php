<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceContentReports extends Migration
{
    public function up()
    {
        $fields = [
            'reviewed_by' => [
                'type' => 'BIGINT',
                'null' => true,
                'after' => 'status',
            ],
            'reviewed_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'after' => 'reviewed_by',
            ],
            'resolution_notes' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'reviewed_at',
            ],
        ];

        $this->forge->addColumn('content_reports', $fields);

        // Add foreign key for reviewed_by
        $this->forge->addForeignKey('reviewed_by', 'users', 'id', 'SET NULL', 'CASCADE');

        // Add index for reviewed_by lookups
        $this->forge->addKey('reviewed_by');

        // Update status constraint to include 'escalated'
        $db = \Config\Database::connect();
        $db->query("ALTER TABLE content_reports DROP CONSTRAINT IF EXISTS content_reports_status_check");
        $db->query("ALTER TABLE content_reports ADD CONSTRAINT content_reports_status_check CHECK (status IN ('pending', 'reviewed', 'resolved', 'dismissed', 'escalated'))");
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $db->query("ALTER TABLE content_reports DROP CONSTRAINT IF EXISTS content_reports_status_check");
        $db->query("ALTER TABLE content_reports ADD CONSTRAINT content_reports_status_check CHECK (status IN ('pending', 'reviewed', 'resolved', 'dismissed'))");
        
        $this->forge->dropColumn('content_reports', ['reviewed_by', 'reviewed_at', 'resolution_notes']);
    }
}

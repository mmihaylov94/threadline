<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCategoryModerationFields extends Migration
{
    public function up()
    {
        // Add moderation fields to categories table
        $fields = [
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'pending',
            ],
            'created_by' => [
                'type' => 'BIGINT',
                'null' => true,
            ],
            'reviewed_by' => [
                'type' => 'BIGINT',
                'null' => true,
            ],
            'reviewed_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'rejection_reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ];

        $this->forge->addColumn('categories', $fields);

        // Add indexes
        $this->forge->addKey('status');
        $this->forge->addKey('created_by');

        // Add foreign keys
        $this->forge->addForeignKey('created_by', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->addForeignKey('reviewed_by', 'users', 'id', 'SET NULL', 'CASCADE');

        // Add CHECK constraint for status (PostgreSQL syntax)
        $db = \Config\Database::connect();
        $db->query("ALTER TABLE categories ADD CONSTRAINT ck_categories_status CHECK (status IN ('pending', 'approved', 'rejected'))");

        // Set existing categories to 'approved' status
        $this->db->table('categories')->update(['status' => 'approved']);
    }

    public function down()
    {
        $db = \Config\Database::connect();
        
        // Drop CHECK constraint
        $db->query("ALTER TABLE categories DROP CONSTRAINT IF EXISTS ck_categories_status");

        // Drop foreign keys (PostgreSQL will auto-drop when columns are dropped, but we'll try to drop them explicitly)
        // Note: Column drops will cascade and remove foreign keys automatically
        $this->forge->dropColumn('categories', ['status', 'created_by', 'reviewed_by', 'reviewed_at', 'rejection_reason']);
    }
}

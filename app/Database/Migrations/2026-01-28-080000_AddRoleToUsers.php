<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoleToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'role' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'member',
                'after'      => 'status',
            ],
        ];

        $this->forge->addColumn('users', $fields);

        // Add CHECK constraint for PostgreSQL compatibility
        $db = \Config\Database::connect();
        $db->query("ALTER TABLE users ADD CONSTRAINT ck_users_role CHECK (role IN ('admin', 'moderator', 'member'))");
        
        // Add index for role lookups
        $this->forge->addKey('role');
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $db->query("ALTER TABLE users DROP CONSTRAINT IF EXISTS ck_users_role");
        $this->forge->dropColumn('users', 'role');
    }
}

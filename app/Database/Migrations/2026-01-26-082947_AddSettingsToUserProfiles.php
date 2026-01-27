<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSettingsToUserProfiles extends Migration
{
    public function up()
    {
        $fields = [
            'timezone' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => 'UTC',
            ],
            'theme_mode' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
                'default'    => 'auto',
            ],
        ];

        $this->forge->addColumn('user_profiles', $fields);

        // Add constraint for theme_mode
        $this->db->query("
            ALTER TABLE user_profiles
            ADD CONSTRAINT ck_user_profiles_theme_mode
            CHECK (theme_mode IN ('light','dark','auto') OR theme_mode IS NULL)
        ");
    }

    public function down()
    {
        // Drop constraint first
        $this->db->query("
            ALTER TABLE user_profiles
            DROP CONSTRAINT IF EXISTS ck_user_profiles_theme_mode
        ");

        $this->forge->dropColumn('user_profiles', ['timezone', 'theme_mode']);
    }
}

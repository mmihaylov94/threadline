<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBackgroundImageToThreads extends Migration
{
    public function up()
    {
        $this->forge->addColumn('threads', [
            'background_image' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('threads', 'background_image');
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEditedAtToPosts extends Migration
{
    public function up()
    {
        $fields = [
            'edited_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ];
        $this->forge->addColumn('posts', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('posts', 'edited_at');
    }
}

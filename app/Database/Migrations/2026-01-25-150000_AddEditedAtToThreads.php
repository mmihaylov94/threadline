<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEditedAtToThreads extends Migration
{
    public function up()
    {
        $this->forge->addColumn('threads', [
            'edited_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('threads', 'edited_at');
    }
}

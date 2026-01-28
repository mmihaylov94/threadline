<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEditedByModeratorToThreads extends Migration
{
    public function up()
    {
        $this->forge->addColumn('threads', [
            'edited_by_moderator' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'null' => false,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('threads', 'edited_by_moderator');
    }
}

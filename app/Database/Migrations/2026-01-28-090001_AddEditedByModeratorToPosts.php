<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEditedByModeratorToPosts extends Migration
{
    public function up()
    {
        $this->forge->addColumn('posts', [
            'edited_by_moderator' => [
                'type' => 'BOOLEAN',
                'default' => false,
                'null' => false,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('posts', 'edited_by_moderator');
    }
}

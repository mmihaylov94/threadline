<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVoteScoreToThreads extends Migration
{
    public function up()
    {
        $fields = [
            'vote_score' => [
                'type'       => 'INT',
                'default'    => 0,
            ],
        ];
        $this->forge->addColumn('threads', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('threads', ['vote_score']);
    }
}

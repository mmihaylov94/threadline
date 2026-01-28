<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVoteScoreToPosts extends Migration
{
    public function up()
    {
        $fields = [
            'vote_score' => [
                'type'       => 'INT',
                'default'    => 0,
            ],
            'upvote_count' => [
                'type'       => 'INT',
                'default'    => 0,
            ],
            'downvote_count' => [
                'type'       => 'INT',
                'default'    => 0,
            ],
        ];
        $this->forge->addColumn('posts', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('posts', ['vote_score', 'upvote_count', 'downvote_count']);
    }
}

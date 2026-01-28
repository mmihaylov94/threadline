<?php

namespace App\Models;

use CodeIgniter\Model;

class ThreadVoteModel extends Model
{
    protected $table         = 'thread_votes';
    protected $returnType    = 'array';
    protected $allowedFields = ['user_id', 'thread_id', 'value'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Set vote for (user, thread).
     * - value = 1 (upvote), -1 (downvote), 0 (remove vote)
     * Returns final value stored: 1, -1, or 0.
     */
    public function setVote(int $userId, int $threadId, int $value): int
    {
        $value = $value === 1 ? 1 : ($value === -1 ? -1 : 0);

        $existing = $this->where('user_id', $userId)->where('thread_id', $threadId)->first();
        if (!$existing) {
            if ($value === 0) {
                return 0;
            }
            $this->insert(['user_id' => $userId, 'thread_id' => $threadId, 'value' => $value]);
            return $value;
        }

        if ($value === 0) {
            $this->delete($existing['id']);
            return 0;
        }

        $this->update($existing['id'], ['value' => $value]);
        return $value;
    }

    public function getUserVote(int $userId, int $threadId): int
    {
        $row = $this->select('value')->where('user_id', $userId)->where('thread_id', $threadId)->first();
        return $row ? (int) $row['value'] : 0;
    }
}


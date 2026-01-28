<?php

namespace App\Models;

use CodeIgniter\Model;

class FavoriteModel extends Model
{
    protected $table            = 'user_favorites';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id',
        'thread_id',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    /**
     * Toggle favorite status for a thread
     * Returns true if added, false if removed
     */
    public function toggleFavorite(int $userId, int $threadId): bool
    {
        $existing = $this->where('user_id', $userId)
            ->where('thread_id', $threadId)
            ->first();

        if ($existing) {
            // Remove favorite
            $this->delete($existing['id']);
            return false;
        } else {
            // Add favorite
            $this->insert([
                'user_id'   => $userId,
                'thread_id' => $threadId,
            ]);
            return true;
        }
    }

    /**
     * Check if a thread is favorited by a user
     */
    public function isFavorite(int $userId, int $threadId): bool
    {
        return $this->where('user_id', $userId)
            ->where('thread_id', $threadId)
            ->first() !== null;
    }

    /**
     * Get user's favorite threads
     */
    public function getUserFavorites(int $userId, int $limit = 10): array
    {
        return $this->select('user_favorites.*, threads.id, threads.slug, threads.title, threads.created_at, threads.last_post_at, categories.name as category_name, categories.slug as category_slug')
            ->join('threads', 'threads.id = user_favorites.thread_id')
            ->join('categories', 'categories.id = threads.category_id')
            ->where('user_favorites.user_id', $userId)
            ->where('threads.deleted_at', null)
            ->orderBy('user_favorites.created_at', 'DESC')
            ->findAll($limit, 0);
    }

    /**
     * Count user's favorite threads
     */
    public function countUserFavorites(int $userId): int
    {
        return $this->select('user_favorites.*')
            ->join('threads', 'threads.id = user_favorites.thread_id')
            ->where('user_favorites.user_id', $userId)
            ->where('threads.deleted_at', null)
            ->countAllResults(false);
    }
}

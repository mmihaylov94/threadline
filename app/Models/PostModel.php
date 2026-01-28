<?php

namespace App\Models;

use CodeIgniter\Model;

class PostModel extends Model
{
    protected $table            = 'posts';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'thread_id',
        'author_id',
        'parent_id',
        'body',
        'edited_at',
        'edited_by_moderator',
        'vote_score',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'thread_id' => 'required|is_natural_no_zero',
        'body'      => 'required',
    ];

    protected $validationMessages = [
        'thread_id' => ['required' => 'Thread ID is required.'],
        'body'      => ['required' => 'Post body is required.'],
    ];

    /**
     * Get posts for a thread, paginated, with author username
     * Excludes soft-deleted posts
     * Orders by vote score (up-down) then created_at
     */
    public function getPaginatedByThread(int $threadId, int $perPage = 15, ?int $page = null): array
    {
        $posts = $this->select('posts.*, users.username as author_username, user_profiles.display_name as author_display_name, user_profiles.avatar_path as author_avatar_path')
            ->join('users', 'users.id = posts.author_id')
            ->join('user_profiles', 'user_profiles.user_id = users.id', 'left')
            ->where('posts.thread_id', $threadId)
            ->where('posts.deleted_at', null)
            ->orderBy('posts.vote_score', 'DESC')
            ->orderBy('posts.created_at', 'ASC')
            ->paginate($perPage, 'default', $page);

        return ['posts' => $posts, 'pager' => $this->pager];
    }

    /**
     * Get recent posts by author
     */
    public function getByAuthor(int $userId, int $limit = 10): array
    {
        return $this->select('posts.id, posts.body, posts.created_at, threads.id as thread_id, threads.slug as thread_slug, threads.title as thread_title')
            ->join('threads', 'threads.id = posts.thread_id')
            ->where('posts.author_id', $userId)
            ->orderBy('posts.created_at', 'DESC')
            ->findAll($limit, 0);
    }

    /**
     * Count posts by author
     */
    public function countByAuthor(int $userId): int
    {
        return $this->where('posts.author_id', $userId)->countAllResults(false);
    }

    /**
     * Soft delete a post
     */
    public function softDelete(int $postId): bool
    {
        $post = $this->find($postId);
        if (!$post) {
            return false;
        }
        
        // Check if already deleted
        if (!empty($post['deleted_at'])) {
            return true; // Already deleted
        }
        
        // Use query builder to avoid exception when no rows affected
        $db = $this->db;
        $result = $db->table($this->table)
            ->where('id', $postId)
            ->where('deleted_at', null)
            ->update(['deleted_at' => date('Y-m-d H:i:s')]);
        
        return $result !== false;
    }
}

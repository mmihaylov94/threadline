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
     */
    public function getPaginatedByThread(int $threadId, int $perPage = 15, ?int $page = null): array
    {
        $posts = $this->select('posts.*, users.username as author_username')
            ->join('users', 'users.id = posts.author_id')
            ->where('posts.thread_id', $threadId)
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
}

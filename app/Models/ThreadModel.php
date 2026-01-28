<?php

namespace App\Models;

use CodeIgniter\Model;

class ThreadModel extends Model
{
    protected $table            = 'threads';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'category_id',
        'author_id',
        'title',
        'slug',
        'body',
        'locked',
        'post_count',
        'last_post_at',
        'vote_score',
        'edited_at',
        'edited_by_moderator',
        'background_image',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules = [
        'category_id' => 'required|is_natural_no_zero|is_not_unique[categories.id]',
        'title'       => 'required|max_length[255]',
        'body'             => 'required',
        'background_image' => 'permit_empty|max_length[500]',
    ];

    protected $validationMessages = [
        'category_id' => [
            'required'         => 'Please select a category.',
            'is_natural_no_zero' => 'Invalid category.',
            'is_not_unique'    => 'Invalid category.',
        ],
        'title' => [
            'required'   => 'Title is required.',
            'max_length' => 'Title cannot exceed 255 characters.',
        ],
        'body' => [
            'required' => 'Body is required.',
        ],
    ];

    /**
     * Get recent threads paginated, with category and author.
     * Optionally filtered by category slug and/or case-insensitive search on title and body.
     * Supports sorting: 'newest', 'most_replies', 'top', 'latest_activity' (default)
     */
    public function getRecentPaginated(int $perPage = 20, ?string $categorySlug = null, ?string $search = null, ?string $sort = null): array
    {
        $builder = $this->select('threads.*, categories.name as category_name, categories.slug as category_slug, users.username as author_username, user_profiles.display_name as author_display_name, user_profiles.avatar_path as author_avatar_path')
            ->join('categories', 'categories.id = threads.category_id')
            ->join('users', 'users.id = threads.author_id')
            ->join('user_profiles', 'user_profiles.user_id = users.id', 'left')
            ->where('threads.deleted_at', null);

        if ($categorySlug !== null && $categorySlug !== '') {
            $builder->where('categories.slug', $categorySlug);
        }

        if ($search !== null && $search !== '') {
            $term = '%' . strtolower(trim($search)) . '%';
            $builder->groupStart()
                ->where('LOWER(threads.title) LIKE', $term)
                ->orWhere('LOWER(threads.body) LIKE', $term)
                ->groupEnd();
        }

        // Apply sorting
        if ($sort === 'newest') {
            $builder->orderBy('threads.created_at', 'DESC');
        } elseif ($sort === 'most_replies') {
            $builder->orderBy('threads.post_count', 'DESC');
        } elseif ($sort === 'top') {
            $builder->orderBy('threads.vote_score', 'DESC');
            $builder->orderBy('COALESCE(threads.last_post_at, threads.created_at)', 'DESC', false);
        } else {
            // Default: latest_activity
            $builder->orderBy('COALESCE(threads.last_post_at, threads.created_at)', 'DESC', false);
        }

        $threads = $builder->paginate($perPage);

        return ['threads' => $threads, 'pager' => $this->pager];
    }

    /**
     * Find thread by slug with category and author
     */
    public function findBySlug(string $slug)
    {
        return $this->select('threads.*, categories.name as category_name, categories.slug as category_slug, users.username as author_username, user_profiles.display_name as author_display_name, user_profiles.avatar_path as author_avatar_path')
            ->join('categories', 'categories.id = threads.category_id')
            ->join('users', 'users.id = threads.author_id')
            ->join('user_profiles', 'user_profiles.user_id = users.id', 'left')
            ->where('threads.slug', $slug)
            ->first();
    }

    /**
     * Generate a unique slug from a title
     */
    public function generateSlug(string $title): string
    {
        helper('text');
        $base = url_title($title, '-', true);
        if ($base === '') {
            $base = 'thread';
        }
        $slug = $base;
        $n = 2;
        while ($this->where('slug', $slug)->first() !== null) {
            $slug = $base . '-' . $n;
            $n++;
        }
        return $slug;
    }

    /**
     * Increment post count for a thread
     */
    public function incrementPostCount(int $threadId): bool
    {
        $thread = $this->find($threadId);
        if (!$thread) {
            return false;
        }
        return $this->update($threadId, ['post_count' => (int) ($thread['post_count'] ?? 0) + 1]);
    }

    /**
     * Decrement post count for a thread
     */
    public function decrementPostCount(int $threadId): bool
    {
        $thread = $this->find($threadId);
        if (!$thread) {
            return false;
        }
        $currentCount = (int) ($thread['post_count'] ?? 0);
        if ($currentCount <= 0) {
            return true; // Already at 0, nothing to do
        }
        return $this->update($threadId, ['post_count' => $currentCount - 1]);
    }

    /**
     * Update last_post_at for a thread
     */
    public function updateLastPostAt(int $threadId, string $at): bool
    {
        return $this->update($threadId, ['last_post_at' => $at]);
    }

    /**
     * Get recent threads for the home page (no pagination)
     */
    public function getRecentForHome(int $limit = 4): array
    {
        return $this->select('threads.id, threads.slug, threads.title, threads.body, threads.background_image, threads.created_at, threads.last_post_at, categories.name as category_name, categories.slug as category_slug')
            ->join('categories', 'categories.id = threads.category_id')
            ->orderBy('COALESCE(threads.last_post_at, threads.created_at)', 'DESC', false)
            ->findAll($limit, 0);
    }

    /**
     * Get recent threads by author
     */
    public function getByAuthor(int $userId, int $limit = 10): array
    {
        return $this->select('threads.id, threads.slug, threads.title, threads.created_at, threads.last_post_at, categories.name as category_name, categories.slug as category_slug')
            ->join('categories', 'categories.id = threads.category_id')
            ->where('threads.author_id', $userId)
            ->orderBy('threads.created_at', 'DESC')
            ->findAll($limit, 0);
    }

    /**
     * Count threads by author
     */
    public function countByAuthor(int $userId): int
    {
        return $this->where('threads.author_id', $userId)->countAllResults(false);
    }

    /**
     * Get recent threads for sidebar
     */
    public function getRecentForSidebar(int $limit = 5): array
    {
        return $this->select('threads.id, threads.slug, threads.title, threads.created_at, threads.last_post_at, categories.name as category_name, categories.slug as category_slug')
            ->join('categories', 'categories.id = threads.category_id')
            ->where('threads.deleted_at', null)
            ->orderBy('COALESCE(threads.last_post_at, threads.created_at)', 'DESC', false)
            ->findAll($limit, 0);
    }

    /**
     * Get favorite threads for a user (for sidebar)
     */
    public function getFavoriteThreads(int $userId, int $limit = 5): array
    {
        return $this->select('threads.id, threads.slug, threads.title, threads.created_at, threads.last_post_at, categories.name as category_name, categories.slug as category_slug')
            ->join('categories', 'categories.id = threads.category_id')
            ->join('user_favorites', 'user_favorites.thread_id = threads.id')
            ->where('user_favorites.user_id', $userId)
            ->where('threads.deleted_at', null)
            ->orderBy('user_favorites.created_at', 'DESC')
            ->findAll($limit, 0);
    }

    /**
     * Soft delete a thread
     */
    public function softDelete(int $threadId): bool
    {
        $thread = $this->find($threadId);
        if (!$thread) {
            return false;
        }
        
        // Check if already deleted
        if (!empty($thread['deleted_at'])) {
            return true; // Already deleted
        }
        
        // Use query builder to avoid exception when no rows affected
        $db = $this->db;
        $result = $db->table($this->table)
            ->where('id', $threadId)
            ->where('deleted_at', null)
            ->update(['deleted_at' => date('Y-m-d H:i:s')]);
        
        return $result !== false;
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table            = 'categories';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'name',
        'slug',
        'description',
        'sort_order',
        'status',
        'created_by',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name' => 'required|max_length[100]',
        'slug' => 'required|max_length[100]|is_unique[categories.slug,id,{id}]',
        'status' => 'permit_empty|in_list[pending,approved,rejected]',
    ];

    protected $validationMessages = [
        'name' => [
            'required'   => 'Category name is required.',
            'max_length' => 'Category name cannot exceed 100 characters.',
        ],
        'slug' => [
            'required'   => 'Category slug is required.',
            'max_length' => 'Category slug cannot exceed 100 characters.',
            'is_unique'  => 'This category slug is already in use.',
        ],
    ];

    /**
     * Find category by slug
     * By default, only finds approved categories unless $includeAll is true
     */
    public function findBySlug(string $slug, bool $includeAll = false)
    {
        $builder = $this->where('slug', $slug);
        if (!$includeAll) {
            $builder->where('status', 'approved');
        }
        return $builder->first();
    }

    /**
     * Get all categories for dropdowns, ordered by sort_order and name
     * By default, only returns approved categories unless $includeAll is true
     */
    public function getAllForSelect(bool $includeAll = false): array
    {
        $builder = $this->orderBy('sort_order', 'asc')->orderBy('name', 'asc');
        
        if (!$includeAll) {
            $builder->where('status', 'approved');
        }
        
        return $builder->findAll();
    }

    /**
     * Get only approved categories (for public display)
     */
    public function getApprovedCategories(): array
    {
        return $this->where('status', 'approved')
            ->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc')
            ->findAll();
    }

    /**
     * Get pending categories (for moderation)
     */
    public function getPendingCategories(): array
    {
        return $this->select('categories.*, users.username as creator_username')
            ->join('users', 'users.id = categories.created_by', 'left')
            ->where('categories.status', 'pending')
            ->orderBy('categories.created_at', 'asc')
            ->findAll();
    }

    /**
     * Get categories by status
     */
    public function getCategoriesByStatus(string $status): array
    {
        return $this->select('categories.*, creator.username as creator_username, reviewer.username as reviewer_username')
            ->join('users as creator', 'creator.id = categories.created_by', 'left')
            ->join('users as reviewer', 'reviewer.id = categories.reviewed_by', 'left')
            ->where('categories.status', $status)
            ->orderBy('categories.created_at', 'desc')
            ->findAll();
    }

    /**
     * Check if slug exists (across all statuses)
     */
    public function slugExists(string $slug, ?int $excludeId = null): bool
    {
        $builder = $this->where('slug', $slug);
        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
    }
}

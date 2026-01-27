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
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name' => 'required|max_length[100]',
        'slug' => 'required|max_length[100]|is_unique[categories.slug,id,{id}]',
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
     */
    public function findBySlug(string $slug)
    {
        return $this->where('slug', $slug)->first();
    }

    /**
     * Get all categories for dropdowns, ordered by sort_order and name
     */
    public function getAllForSelect(): array
    {
        return $this->orderBy('sort_order', 'asc')->orderBy('name', 'asc')->findAll();
    }
}

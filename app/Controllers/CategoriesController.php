<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\FavoriteModel;
use App\Models\ThreadModel;

class CategoriesController extends BaseController
{
    /**
     * List all categories. Minimal: name, description, link to threads filtered by slug.
     * Only shows approved categories to regular users.
     */
    public function index()
    {
        $categoryModel = model(CategoryModel::class);
        $threadModel = model(ThreadModel::class);
        
        // Only show approved categories to regular users
        $categories = $categoryModel->getApprovedCategories();

        // Get sidebar data
        $recentThreads = $threadModel->getRecentForSidebar(5);
        $favoriteThreads = [];
        if ($this->isLoggedIn()) {
            $favoriteThreads = $threadModel->getFavoriteThreads($this->getCurrentUserId(), 5);
        }

        return view('categories/index', [
            'title'      => 'Categories',
            'categories' => $categories,
            'recentThreads'  => $recentThreads,
            'favoriteThreads' => $favoriteThreads,
        ]);
    }

    /**
     * Create a new category (GET/POST)
     * Authenticated users can submit category requests
     */
    public function create()
    {
        if (!$this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'You must be logged in to create a category.');
        }

        $categoryModel = model(CategoryModel::class);
        helper('text');

        // Check if user is moderator/admin
        $isModerator = $this->isModerator();

        if (strtolower($this->request->getMethod()) !== 'post') {
            return view('categories/create', [
                'title' => 'Create Category',
                'isModerator' => $isModerator,
            ]);
        }

        $name = $this->request->getPost('name');
        $description = $this->request->getPost('description');

        if (empty($name)) {
            return redirect()->back()->withInput()->with('error', 'Category name is required.');
        }

        // Generate slug from name
        $slug = url_title($name, '-', true);
        if (empty($slug)) {
            $slug = 'category-' . time();
        }

        // Check if slug already exists (across all statuses)
        $counter = 1;
        $originalSlug = $slug;
        while ($categoryModel->slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        // Check if user is moderator/admin - they can create approved categories directly
        $isModerator = $this->isModerator();
        $currentUserId = $this->getCurrentUserId();

        $data = [
            'name' => $name,
            'slug' => $slug,
            'description' => $description ? trim($description) : null,
            'status' => $isModerator ? 'approved' : 'pending',
            'created_by' => $currentUserId,
            'sort_order' => 0,
        ];

        // If moderator/admin, set reviewed fields immediately
        if ($isModerator) {
            $data['reviewed_by'] = $currentUserId;
            $data['reviewed_at'] = date('Y-m-d H:i:s');
        }

        if (!$categoryModel->validate($data)) {
            return redirect()->back()->withInput()->with('errors', $categoryModel->errors());
        }

        if ($categoryModel->insert($data) === false) {
            return redirect()->back()->withInput()->with('error', 'Could not create category request.');
        }

        $categoryId = $categoryModel->getInsertID();

        // Log category creation
        $auditLogService = service('auditLog');
        $auditLogService->log(
            'category.created',
            'category',
            $categoryId,
            [
                'name' => $name,
                'slug' => $slug,
                'status' => $isModerator ? 'approved' : 'pending',
            ],
            $currentUserId
        );

        // If moderator/admin created it, also log as approved
        if ($isModerator) {
            $auditLogService->log(
                'category.approved',
                'category',
                $categoryId,
                [
                    'name' => $name,
                    'slug' => $slug,
                    'auto_approved' => true,
                ],
                $currentUserId
            );
        }

        $successMessage = $isModerator 
            ? 'Category created successfully and is now publicly available.'
            : 'Category request submitted successfully. It will be reviewed by moderators before being made public.';

        return redirect()->to('/categories')->with('success', $successMessage);
    }
}

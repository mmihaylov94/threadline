<?php

namespace App\Controllers;

use App\Models\CategoryModel;

class CategoriesController extends BaseController
{
    /**
     * List all categories. Minimal: name, description, link to threads filtered by slug.
     */
    public function index()
    {
        $categoryModel = model(CategoryModel::class);
        $categories    = $categoryModel->orderBy('sort_order', 'asc')->orderBy('name', 'asc')->findAll();

        return view('categories/index', [
            'title'      => 'Categories',
            'categories' => $categories,
        ]);
    }
}

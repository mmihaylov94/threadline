<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\PostModel;
use App\Models\ThreadModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ThreadsController extends BaseController
{
    /**
     * List recent threads, optionally filtered by category. Paginated.
     */
    public function index()
    {
        $threadModel  = model(ThreadModel::class);
        $categoryModel = model(CategoryModel::class);

        $categorySlug = $this->request->getGet('category');
        $filterCategory = null;
        if ($categorySlug !== null && $categorySlug !== '') {
            $filterCategory = $categoryModel->findBySlug($categorySlug);
            if ($filterCategory) {
                $categorySlug = $filterCategory['slug'];
            } else {
                $categorySlug = null;
            }
        }

        $result = $threadModel->getRecentPaginated(10, $categorySlug);
        $categories = $categoryModel->getAllForSelect();

        return view('threads/list', [
            'title'          => 'Recent threads',
            'threads'        => $result['threads'],
            'pager'          => $result['pager'],
            'categories'     => $categories,
            'filterCategory' => $filterCategory,
        ]);
    }

    /**
     * View a thread by slug. Paginated replies.
     */
    public function view(string $slug, $page = 1)
    {
        $threadModel = model(ThreadModel::class);
        $postModel   = model(PostModel::class);

        $thread = $threadModel->findBySlug($slug);
        if (!$thread) {
            throw PageNotFoundException::forPageNotFound();
        }

        $page = (int) $page;
        if ($page < 1) {
            $page = 1;
        }

        $result = $postModel->getPaginatedByThread((int) $thread['id'], 15, $page);

        return view('threads/view', [
            'title'  => esc($thread['title']),
            'thread' => $thread,
            'posts'  => $result['posts'],
            'pager'  => $result['pager'],
        ]);
    }

    /**
     * Create a new thread (GET: form, POST: save). Auth required.
     */
    public function create()
    {
        $threadModel  = model(ThreadModel::class);
        $categoryModel = model(CategoryModel::class);

        if (strtolower($this->request->getMethod()) !== 'post') {
            $categories = $categoryModel->getAllForSelect();
            return view('threads/create', [
                'title'      => 'New thread',
                'categories' => $categories,
            ]);
        }

        $categoryId = $this->request->getPost('category_id');
        $title      = $this->request->getPost('title');
        $body       = $this->request->getPost('body');
        
        // Handle file upload
        $bgImage = $this->handleBackgroundImageUpload();

        // Sanitize HTML body
        $body = sanitizeHtml($body);

        $data = [
            'category_id'      => $categoryId,
            'author_id'        => $this->getCurrentUserId(),
            'title'            => $title,
            'slug'             => $threadModel->generateSlug($title ?: 'thread'),
            'body'             => $body,
            'background_image' => $bgImage,
        ];

        if (!$threadModel->validate($data)) {
            return redirect()->back()->withInput()->with('errors', $threadModel->errors());
        }

        $id = $threadModel->insert($data);
        if ($id === false) {
            return redirect()->back()->withInput()->with('error', 'Could not create the thread.');
        }

        $inserted = $threadModel->find($id);
        return redirect()->to('/threads/' . $inserted['slug'])->with('success', 'Thread created.');
    }

    /**
     * Edit an existing thread (GET: form, POST: save). Auth required. Author only.
     */
    public function edit($id)
    {
        helper('content');
        
        $threadModel  = model(ThreadModel::class);
        $categoryModel = model(CategoryModel::class);

        $thread = $threadModel->find((int) $id);
        if (!$thread) {
            throw PageNotFoundException::forPageNotFound();
        }

        if ((int) $thread['author_id'] !== (int) $this->getCurrentUserId()) {
            return redirect()->to('/threads/' . $thread['slug'])->with('error', 'You can only edit your own threads.');
        }

        if (strtolower($this->request->getMethod()) !== 'post') {
            $categories = $categoryModel->getAllForSelect();
            return view('threads/edit', [
                'title'      => 'Edit thread',
                'thread'     => $thread,
                'categories' => $categories,
            ]);
        }

        $categoryId = $this->request->getPost('category_id');
        $title      = $this->request->getPost('title');
        $body       = $this->request->getPost('body');
        
        // Handle file upload (optional - keep existing if not uploaded)
        $bgImage = $this->handleBackgroundImageUpload();
        if ($bgImage === null && !empty($thread['background_image'])) {
            $bgImage = $thread['background_image']; // Keep existing
        } elseif ($bgImage !== null && !empty($thread['background_image'])) {
            // Delete old file if new one uploaded
            $this->deleteBackgroundImage($thread['background_image']);
        }

        // Sanitize HTML body
        $body = sanitizeHtml($body);

        $data = [
            'category_id'      => $categoryId,
            'title'            => $title,
            'body'             => $body,
            'edited_at'        => date('Y-m-d H:i:s'),
            'background_image' => $bgImage,
        ];

        if (!$threadModel->validate($data)) {
            return redirect()->back()->withInput()->with('errors', $threadModel->errors());
        }

        if ($threadModel->update((int) $id, $data) === false) {
            return redirect()->back()->withInput()->with('error', 'Could not update the thread.');
        }

        $updated = $threadModel->find((int) $id);
        return redirect()->to('/threads/' . $updated['slug'])->with('success', 'Thread updated.');
    }

    /**
     * Reply to a thread. Auth required.
     */
    public function reply(string $slug)
    {
        $threadModel = model(ThreadModel::class);
        $postModel   = model(PostModel::class);

        $thread = $threadModel->findBySlug($slug);
        if (!$thread) {
            throw PageNotFoundException::forPageNotFound();
        }

        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to('/threads/' . $slug);
        }

        $body = $this->request->getPost('body');
        if (empty($body)) {
            return redirect()->to('/threads/' . $slug)->with('error', 'Reply body is required.');
        }

        // Sanitize HTML body
        $body = sanitizeHtml($body);

        $data = [
            'thread_id' => (int) $thread['id'],
            'author_id' => $this->getCurrentUserId(),
            'body'      => $body,
        ];

        if (!$postModel->validate($data)) {
            return redirect()->to('/threads/' . $slug)->with('errors', $postModel->errors());
        }

        $postId = $postModel->insert($data);
        if ($postId === false) {
            return redirect()->to('/threads/' . $slug)->with('error', 'Could not create the reply.');
        }

        // Update thread stats
        $threadModel->incrementPostCount((int) $thread['id']);
        $threadModel->updateLastPostAt((int) $thread['id'], date('Y-m-d H:i:s'));

        return redirect()->to('/threads/' . $slug)->with('success', 'Reply posted.');
    }

    /**
     * Handle background image file upload.
     *
     * @return string|null URL/path to uploaded file, or null if no file uploaded
     */
    protected function handleBackgroundImageUpload(): ?string
    {
        $file = $this->request->getFile('background_image');
        
        if (!$file || !$file->isValid()) {
            return null;
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return null;
        }

        // Validate file size (5MB max)
        if ($file->getSize() > 5 * 1024 * 1024) {
            return null;
        }

        // Create upload directory if it doesn't exist (in public folder)
        $uploadPath = FCPATH . 'uploads/threads/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Generate unique filename
        $newName = $file->getRandomName();
        if ($file->move($uploadPath, $newName)) {
            // Return relative URL path
            return base_url('uploads/threads/' . $newName);
        }

        return null;
    }

    /**
     * Delete background image file.
     *
     * @param string $imagePath URL or path to image
     */
    protected function deleteBackgroundImage(string $imagePath): void
    {
        // Extract filename from URL
        if (strpos($imagePath, 'uploads/threads/') !== false) {
            $filename = basename($imagePath);
            $filePath = FCPATH . 'uploads/threads/' . $filename;
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
    }
}

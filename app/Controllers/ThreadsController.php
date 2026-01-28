<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\FavoriteModel;
use App\Models\PostModel;
use App\Models\PostVoteModel;
use App\Models\ReportModel;
use App\Models\ThreadVoteModel;
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

        $searchQuery = $this->request->getGet('q');
        $searchQuery = is_string($searchQuery) ? trim($searchQuery) : '';
        $searchQuery = $searchQuery !== '' ? $searchQuery : null;

        $sort = $this->request->getGet('sort');
        $sort = in_array($sort, ['newest', 'most_replies', 'top', 'latest_activity']) ? $sort : null;

        $result = $threadModel->getRecentPaginated(10, $categorySlug, $searchQuery, $sort);
        $categories = $categoryModel->getAllForSelect();

        // Get sidebar data
        $recentThreads = $threadModel->getRecentForSidebar(5);
        $favoriteThreads = [];
        $favoriteThreadIds = [];
        if ($this->isLoggedIn()) {
            $favoriteModel = model(FavoriteModel::class);
            $favoriteThreads = $threadModel->getFavoriteThreads($this->getCurrentUserId(), 5);

            $threadIdsOnPage = array_values(array_filter(array_map(static function ($t) {
                return isset($t['id']) ? (int) $t['id'] : null;
            }, is_array($result['threads']) ? $result['threads'] : [])));

            if (!empty($threadIdsOnPage)) {
                $rows = $favoriteModel->select('thread_id')
                    ->where('user_id', $this->getCurrentUserId())
                    ->whereIn('thread_id', $threadIdsOnPage)
                    ->findAll();
                $favoriteThreadIds = array_map(static fn ($r) => (int) $r['thread_id'], $rows);
            }
        }

        return view('threads/list', [
            'title'          => 'Recent threads',
            'threads'        => $result['threads'],
            'pager'          => $result['pager'],
            'categories'     => $categories,
            'filterCategory' => $filterCategory,
            'searchQuery'    => $searchQuery,
            'sort'           => $sort,
            'recentThreads'  => $recentThreads,
            'favoriteThreads' => $favoriteThreads,
            'favoriteThreadIds' => $favoriteThreadIds,
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

        // Favorite state + sidebar favorites + user votes
        $isFavorite = false;
        $favoriteThreads = [];
        $threadUserVote = 0;
        $postUserVotes = [];
        if ($this->isLoggedIn()) {
            $favoriteModel = model(FavoriteModel::class);
            $isFavorite = $favoriteModel->isFavorite($this->getCurrentUserId(), (int) $thread['id']);
            $favoriteThreads = $threadModel->getFavoriteThreads($this->getCurrentUserId(), 5);
            
            // Get user vote for thread
            $threadVoteModel = model(ThreadVoteModel::class);
            $threadUserVote = $threadVoteModel->getUserVote($this->getCurrentUserId(), (int) $thread['id']);
            
            // Get user votes for posts
            $postVoteModel = model(PostVoteModel::class);
            foreach ($result['posts'] as $post) {
                $postUserVotes[(int) $post['id']] = $postVoteModel->getUserVote($this->getCurrentUserId(), (int) $post['id']);
            }
        }

        // Check if current user is moderator/admin
        $isModerator = $this->isModerator();

        return view('threads/view', [
            'title'      => esc($thread['title']),
            'thread'     => $thread,
            'posts'      => $result['posts'],
            'pager'      => $result['pager'],
            'isFavorite' => $isFavorite,
            'favoriteThreads' => $favoriteThreads,
            'threadUserVote' => $threadUserVote,
            'postUserVotes' => $postUserVotes,
            'isModerator' => $isModerator,
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

        // Allow author or moderator/admin to edit
        $isAuthor = (int) $thread['author_id'] === (int) $this->getCurrentUserId();
        $isModerator = $this->isModerator();
        
        if (!$isAuthor && !$isModerator) {
            return redirect()->to('/threads/' . $thread['slug'])->with('error', 'You can only edit your own threads.');
        }

        if (strtolower($this->request->getMethod()) !== 'post') {
            $categories = $categoryModel->getAllForSelect();
            return view('threads/edit', [
                'title'      => 'Edit thread',
                'thread'     => $thread,
                'categories' => $categories,
                'isModerator' => $isModerator,
                'isAuthor' => $isAuthor,
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

        // Re-check permissions for POST request
        $isAuthor = (int) $thread['author_id'] === (int) $this->getCurrentUserId();
        $isModerator = $this->isModerator();

        $data = [
            'category_id'      => $categoryId,
            'title'            => $title,
            'body'             => $body,
            'edited_at'        => date('Y-m-d H:i:s'),
            'edited_by_moderator' => (!$isAuthor && $isModerator) ? true : false,
            'background_image' => $bgImage,
        ];

        if (!$threadModel->validate($data)) {
            return redirect()->back()->withInput()->with('errors', $threadModel->errors());
        }

        if ($threadModel->update((int) $id, $data) === false) {
            return redirect()->back()->withInput()->with('error', 'Could not update the thread.');
        }

        // Log if edited by moderator/admin (not the author)
        if (!$isAuthor && $isModerator) {
            $auditLogService = service('auditLog');
            $auditLogService->log(
                'thread.edited_by_moderator',
                'thread',
                $id,
                [
                    'title' => $data['title'] ?? '',
                ],
                $this->getCurrentUserId()
            );
        }

        $updated = $threadModel->find((int) $id);
        $successMessage = $isModerator && !$isAuthor ? 'Thread updated by moderator.' : 'Thread updated.';
        return redirect()->to('/threads/' . $updated['slug'])->with('success', $successMessage);
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

    /**
     * Toggle favorite status for a thread. Auth required.
     */
    public function toggleFavorite(string $slug)
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to('/threads/' . $slug);
        }

        $threadModel = model(ThreadModel::class);
        $favoriteModel = model(FavoriteModel::class);

        $thread = $threadModel->findBySlug($slug);
        if (!$thread) {
            throw PageNotFoundException::forPageNotFound();
        }

        $userId = $this->getCurrentUserId();
        $isFavorite = $favoriteModel->toggleFavorite($userId, (int) $thread['id']);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'isFavorite' => $isFavorite,
            ]);
        }

        return redirect()->back()->with('success', $isFavorite ? 'Thread added to favorites.' : 'Thread removed from favorites.');
    }

    /**
     * Delete a thread. Auth required. Author or Moderator/Admin.
     */
    public function delete(int $id)
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to('/threads');
        }

        $threadModel = model(ThreadModel::class);
        $thread = $threadModel->find((int) $id);

        if (!$thread) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Allow author or moderator/admin to delete
        $isAuthor = (int) $thread['author_id'] === (int) $this->getCurrentUserId();
        $isModerator = $this->isModerator();
        
        if (!$isAuthor && !$isModerator) {
            $threadSlug = $thread['slug'] ?? null;
            if ($threadSlug) {
                return redirect()->to('/threads/' . $threadSlug)->with('error', 'You can only delete your own threads.');
            }
            return redirect()->to('/threads')->with('error', 'You can only delete your own threads.');
        }

        if ($threadModel->softDelete((int) $id)) {
            // Log if deleted by moderator/admin (not the author)
            if (!$isAuthor && $isModerator) {
                $auditLogService = service('auditLog');
                $auditLogService->log(
                    'thread.deleted_by_moderator',
                    'thread',
                    $id,
                    [
                        'title' => $thread['title'] ?? '',
                        'reason' => 'Deleted via thread delete action',
                    ],
                    $this->getCurrentUserId()
                );
            }
            
            $successMessage = $isModerator && !$isAuthor ? 'Thread deleted by moderator.' : 'Thread deleted.';
            return redirect()->to('/threads')->with('success', $successMessage);
        }

        return redirect()->back()->with('error', 'Could not delete the thread.');
    }

    /**
     * Report a thread. Auth required.
     */
    public function report(string $slug)
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to('/threads/' . $slug);
        }

        $threadModel = model(ThreadModel::class);
        $reportModel = model(ReportModel::class);

        $thread = $threadModel->findBySlug($slug);
        if (!$thread) {
            throw PageNotFoundException::forPageNotFound();
        }

        $guidelineViolations = $this->request->getPost('guideline_violations');
        $additionalNotes = $this->request->getPost('additional_notes');

        if (empty($guidelineViolations) || !is_array($guidelineViolations)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Please select at least one guideline violation.',
                ]);
            }
            return redirect()->back()->with('error', 'Please select at least one guideline violation.');
        }

        $data = [
            'reporter_id'         => $this->getCurrentUserId(),
            'content_type'        => 'thread',
            'content_id'          => (int) $thread['id'],
            'guideline_violations' => $guidelineViolations,
            'additional_notes'   => $additionalNotes ? trim($additionalNotes) : null,
        ];

        if ($reportModel->createReport($data)) {
            // Log the report creation
            $auditLogService = service('auditLog');
            $auditLogService->log(
                'report.created',
                'report',
                $reportModel->getInsertID(),
                [
                    'content_type' => 'thread',
                    'content_id' => (int) $thread['id'],
                    'guideline_violations' => $guidelineViolations,
                ],
                $this->getCurrentUserId()
            );

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Report submitted successfully.',
                ]);
            }
            return redirect()->back()->with('success', 'Report submitted successfully.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Could not submit the report.',
            ]);
        }
        return redirect()->back()->with('error', 'Could not submit the report.');
    }

    /**
     * Edit a reply. Auth required. Author or Moderator/Admin.
     */
    public function editReply(int $id)
    {
        helper('content');
        
        $postModel = model(PostModel::class);
        $post = $postModel->find((int) $id);

        if (!$post) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Allow author or moderator/admin to edit
        $isAuthor = (int) $post['author_id'] === (int) $this->getCurrentUserId();
        $isModerator = $this->isModerator();
        
        if (!$isAuthor && !$isModerator) {
            $threadModel = model(ThreadModel::class);
            $thread = $threadModel->find((int) $post['thread_id']);
            return redirect()->to('/threads/' . $thread['slug'])->with('error', 'You can only edit your own replies.');
        }

        if (strtolower($this->request->getMethod()) !== 'post') {
            $threadModel = model(ThreadModel::class);
            $thread = $threadModel->find((int) $post['thread_id']);
            return view('threads/edit_reply', [
                'title' => 'Edit reply',
                'post'  => $post,
                'thread' => $thread,
                'isModerator' => $isModerator,
                'isAuthor' => $isAuthor,
            ]);
        }

        $body = $this->request->getPost('body');
        if (empty($body)) {
            return redirect()->back()->with('error', 'Reply body is required.');
        }

        // Sanitize HTML body
        $body = sanitizeHtml($body);

        // Re-check permissions for POST request
        $isAuthor = (int) $post['author_id'] === (int) $this->getCurrentUserId();
        $isModerator = $this->isModerator();

        $data = [
            'body'      => $body,
            'edited_at' => date('Y-m-d H:i:s'),
            'edited_by_moderator' => (!$isAuthor && $isModerator) ? true : false,
        ];

        if ($postModel->update((int) $id, $data) === false) {
            return redirect()->back()->withInput()->with('error', 'Could not update the reply.');
        }

        // Log if edited by moderator/admin (not the author)
        if (!$isAuthor && $isModerator) {
            $auditLogService = service('auditLog');
            $auditLogService->log(
                'post.edited_by_moderator',
                'post',
                $id,
                [
                    'thread_id' => $post['thread_id'],
                ],
                $this->getCurrentUserId()
            );
        }

        $threadModel = model(ThreadModel::class);
        $thread = $threadModel->find((int) $post['thread_id']);
        $successMessage = $isModerator && !$isAuthor ? 'Reply updated by moderator.' : 'Reply updated.';
        return redirect()->to('/threads/' . $thread['slug'])->with('success', $successMessage);
    }

    /**
     * Delete a reply. Auth required. Author or Moderator/Admin.
     */
    public function deleteReply(int $id)
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to('/threads');
        }

        $postModel = model(PostModel::class);
        $post = $postModel->find((int) $id);

        if (!$post) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Allow author or moderator/admin to delete
        $isAuthor = (int) $post['author_id'] === (int) $this->getCurrentUserId();
        $isModerator = $this->isModerator();
        
        if (!$isAuthor && !$isModerator) {
            $threadModel = model(ThreadModel::class);
            $thread = $threadModel->find((int) $post['thread_id']);
            if ($thread && isset($thread['slug'])) {
                return redirect()->to('/threads/' . $thread['slug'])->with('error', 'You can only delete your own replies.');
            }
            return redirect()->to('/threads')->with('error', 'You can only delete your own replies.');
        }

        if ($postModel->softDelete((int) $id)) {
            $threadModel = model(ThreadModel::class);
            // Decrement post count for the thread
            $threadModel->decrementPostCount((int) $post['thread_id']);
            
            // Log if deleted by moderator/admin (not the author)
            if (!$isAuthor && $isModerator) {
                $auditLogService = service('auditLog');
                $auditLogService->log(
                    'post.deleted_by_moderator',
                    'post',
                    $id,
                    [
                        'thread_id' => $post['thread_id'],
                        'reason' => 'Deleted via reply delete action',
                    ],
                    $this->getCurrentUserId()
                );
            }
            
            $thread = $threadModel->find((int) $post['thread_id']);
            $successMessage = $isModerator && !$isAuthor ? 'Reply deleted by moderator.' : 'Reply deleted.';
            return redirect()->to('/threads/' . $thread['slug'])->with('success', $successMessage);
        }

        return redirect()->back()->with('error', 'Could not delete the reply.');
    }

    /**
     * Report a reply. Auth required.
     */
    public function reportReply(int $id)
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to('/threads');
        }

        $postModel = model(PostModel::class);
        $reportModel = model(ReportModel::class);

        $post = $postModel->find((int) $id);
        if (!$post) {
            throw PageNotFoundException::forPageNotFound();
        }

        $guidelineViolations = $this->request->getPost('guideline_violations');
        $additionalNotes = $this->request->getPost('additional_notes');

        if (empty($guidelineViolations) || !is_array($guidelineViolations)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Please select at least one guideline violation.',
                ]);
            }
            return redirect()->back()->with('error', 'Please select at least one guideline violation.');
        }

        $data = [
            'reporter_id'         => $this->getCurrentUserId(),
            'content_type'        => 'post',
            'content_id'          => (int) $id,
            'guideline_violations' => $guidelineViolations,
            'additional_notes'   => $additionalNotes ? trim($additionalNotes) : null,
        ];

        if ($reportModel->createReport($data)) {
            // Log the report creation
            $auditLogService = service('auditLog');
            $auditLogService->log(
                'report.created',
                'report',
                $reportModel->getInsertID(),
                [
                    'content_type' => 'post',
                    'content_id' => (int) $id,
                    'guideline_violations' => $guidelineViolations,
                ],
                $this->getCurrentUserId()
            );

            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Report submitted successfully.',
                ]);
            }
            return redirect()->back()->with('success', 'Report submitted successfully.');
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Could not submit the report.',
            ]);
        }
        return redirect()->back()->with('error', 'Could not submit the report.');
    }

    /**
     * Vote on a thread. Auth required.
     * POST body: vote=up|down|remove
     */
    public function voteThread(string $slug)
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to('/threads/' . $slug);
        }

        $threadModel = model(ThreadModel::class);
        $voteModel   = model(ThreadVoteModel::class);

        $thread = $threadModel->findBySlug($slug);
        if (!$thread) {
            throw PageNotFoundException::forPageNotFound();
        }

        $vote = (string) ($this->request->getPost('vote') ?? '');
        $value = 0;
        if ($vote === 'up') {
            $value = 1;
        } elseif ($vote === 'down') {
            $value = -1;
        } elseif ($vote === 'remove') {
            $value = 0;
        }

        $userId = (int) $this->getCurrentUserId();

        // Determine previous vote to update score efficiently
        $prev = $voteModel->getUserVote($userId, (int) $thread['id']);
        $final = $voteModel->setVote($userId, (int) $thread['id'], $value);

        $deltaScore = 0;

        if ($prev === 1) { $deltaScore -= 1; }
        if ($prev === -1) { $deltaScore += 1; }
        if ($final === 1) { $deltaScore += 1; }
        if ($final === -1) { $deltaScore -= 1; }

        // Atomic update
        $db = $threadModel->db;
        $db->table('threads')
            ->where('id', (int) $thread['id'])
            ->update([
                'vote_score' => (int) ($thread['vote_score'] ?? 0) + $deltaScore,
            ]);

        $updated = $threadModel->find((int) $thread['id']);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'userVote' => $final,
                'score' => (int) ($updated['vote_score'] ?? 0),
            ]);
        }

        return redirect()->back();
    }

    /**
     * Vote on a post. Auth required.
     * POST body: vote=up|down|remove
     */
    public function votePost(int $id)
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to('/threads');
        }

        $postModel = model(PostModel::class);
        $voteModel = model(PostVoteModel::class);

        $post = $postModel->find((int) $id);
        if (!$post) {
            throw PageNotFoundException::forPageNotFound();
        }

        $vote = (string) ($this->request->getPost('vote') ?? '');
        $value = 0;
        if ($vote === 'up') {
            $value = 1;
        } elseif ($vote === 'down') {
            $value = -1;
        } elseif ($vote === 'remove') {
            $value = 0;
        }

        $userId = (int) $this->getCurrentUserId();
        $prev = $voteModel->getUserVote($userId, (int) $post['id']);
        $final = $voteModel->setVote($userId, (int) $post['id'], $value);

        $deltaScore = 0;

        if ($prev === 1) { $deltaScore -= 1; }
        if ($prev === -1) { $deltaScore += 1; }
        if ($final === 1) { $deltaScore += 1; }
        if ($final === -1) { $deltaScore -= 1; }

        $db = $postModel->db;
        $db->table('posts')
            ->where('id', (int) $post['id'])
            ->update([
                'vote_score' => (int) ($post['vote_score'] ?? 0) + $deltaScore,
            ]);

        $updated = $postModel->find((int) $post['id']);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'userVote' => $final,
                'score' => (int) ($updated['vote_score'] ?? 0),
            ]);
        }

        return redirect()->back();
    }

    /**
     * Moderate a thread. Moderator/Admin only.
     */
    public function moderateThread(int $id)
    {
        $this->requireModerator();

        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to('/threads');
        }

        $threadModel = model(ThreadModel::class);
        $thread = $threadModel->find($id);
        if (!$thread) {
            throw PageNotFoundException::forPageNotFound();
        }

        $action = $this->request->getPost('action');
        $reason = $this->request->getPost('reason');

        $auditLogService = service('auditLog');
        $result = false;

        if ($action === 'delete') {
            $result = $threadModel->softDelete($id);
            if ($result) {
                $auditLogService->log(
                    'thread.deleted_by_moderator',
                    'thread',
                    $id,
                    [
                        'reason' => $reason,
                        'title' => $thread['title'] ?? '',
                    ],
                    $this->getCurrentUserId()
                );
            }
        } elseif ($action === 'lock') {
            // Note: Lock functionality would need to be added to ThreadModel
            // For now, we'll just log it
            $auditLogService->log(
                'thread.locked',
                'thread',
                $id,
                [
                    'reason' => $reason,
                    'title' => $thread['title'] ?? '',
                ],
                $this->getCurrentUserId()
            );
            $result = true;
        }

        if ($result) {
            return redirect()->back()->with('success', 'Thread moderated successfully.');
        }

        return redirect()->back()->with('error', 'Could not moderate the thread.');
    }

    /**
     * Moderate a post. Moderator/Admin only.
     */
    public function moderatePost(int $id)
    {
        $this->requireModerator();

        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to('/threads');
        }

        $postModel = model(PostModel::class);
        $post = $postModel->find($id);
        if (!$post) {
            throw PageNotFoundException::forPageNotFound();
        }

        $action = $this->request->getPost('action');
        $reason = $this->request->getPost('reason');

        $auditLogService = service('auditLog');
        $result = false;

        if ($action === 'delete') {
            $result = $postModel->softDelete($id);
            if ($result) {
                $threadModel = model(ThreadModel::class);
                $threadModel->decrementPostCount((int) $post['thread_id']);

                $auditLogService->log(
                    'post.deleted_by_moderator',
                    'post',
                    $id,
                    [
                        'reason' => $reason,
                        'thread_id' => $post['thread_id'],
                    ],
                    $this->getCurrentUserId()
                );
            }
        }

        if ($result) {
            $threadModel = model(ThreadModel::class);
            $thread = $threadModel->find((int) $post['thread_id']);
            return redirect()->to('/threads/' . $thread['slug'])->with('success', 'Post moderated successfully.');
        }

        return redirect()->back()->with('error', 'Could not moderate the post.');
    }
}

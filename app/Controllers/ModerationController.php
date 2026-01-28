<?php

namespace App\Controllers;

use App\Models\ReportModel;
use App\Models\UserModel;
use App\Models\ThreadModel;
use App\Models\PostModel;
use App\Models\CategoryModel;
use App\Services\ModerationService;
use App\Services\AuditLogService;
use CodeIgniter\Exceptions\PageNotFoundException;

class ModerationController extends BaseController
{
    protected $moderationService;
    protected $auditLogService;
    protected $reportModel;
    protected $userModel;

    public function __construct()
    {
        $this->moderationService = service('moderation');
        $this->auditLogService = service('auditLog');
        $this->reportModel = model(ReportModel::class);
        $this->userModel = model(UserModel::class);
    }

    /**
     * Moderation dashboard overview
     */
    public function index()
    {
        $this->requireModerator();

        $reportModel = model(ReportModel::class);
        $auditLogModel = model(\App\Models\AuditLogModel::class);
        $categoryModel = model(CategoryModel::class);

        // Get pending reports count
        $pendingReports = $reportModel->getPendingReports(10);
        $pendingCount = $reportModel->where('status', 'pending')->countAllResults();

        // Get pending categories count
        $pendingCategoriesCount = $categoryModel->where('status', 'pending')->countAllResults();

        // Get stats
        $today = date('Y-m-d');
        $reportsToday = $reportModel->where('DATE(created_at)', $today)->countAllResults();
        $resolvedThisWeek = $reportModel->where('status', 'resolved')
            ->where('DATE(created_at) >=', date('Y-m-d', strtotime('-7 days')))
            ->countAllResults();

        // Get recent audit logs
        $recentLogs = $auditLogModel->getLogs([], 10);

        return view('moderation/dashboard', [
            'title' => 'Moderation Dashboard',
            'pendingReports' => $pendingReports,
            'pendingCount' => $pendingCount,
            'pendingCategoriesCount' => $pendingCategoriesCount,
            'reportsToday' => $reportsToday,
            'resolvedThisWeek' => $resolvedThisWeek,
            'recentLogs' => $recentLogs,
            'isAdmin' => $this->isAdmin(),
        ]);
    }

    /**
     * List reports with filtering
     */
    public function reports()
    {
        $this->requireModerator();

        $status = $this->request->getGet('status');
        $contentType = $this->request->getGet('content_type');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');

        $filters = [];
        if ($status) {
            $filters['status'] = $status;
        }
        if ($contentType) {
            $filters['content_type'] = $contentType;
        }
        if ($dateFrom) {
            $filters['date_from'] = $dateFrom;
        }
        if ($dateTo) {
            $filters['date_to'] = $dateTo;
        }

        $reports = $this->reportModel->getReportsForModeration($filters, 50);

        return view('moderation/reports', [
            'title' => 'Reports',
            'reports' => $reports,
            'filters' => [
                'status' => $status,
                'content_type' => $contentType,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }

    /**
     * View a single report
     */
    public function viewReport(int $id)
    {
        $this->requireModerator();

        $report = $this->moderationService->getReportWithContent($id);
        if (!$report) {
            throw PageNotFoundException::forPageNotFound();
        }

        // Load the actual content
        $content = null;
        if ($report['content_type'] === 'thread') {
            $threadModel = model(ThreadModel::class);
            $content = $threadModel->find($report['content_id']);
        } else {
            $postModel = model(PostModel::class);
            $content = $postModel->find($report['content_id']);
        }

        // Get audit logs for this report
        $auditLogs = $this->auditLogService->getEntityLogs('report', $id, 20);

        return view('moderation/view_report', [
            'title' => 'View Report',
            'report' => $report,
            'content' => $content,
            'auditLogs' => $auditLogs,
            'isAdmin' => $this->isAdmin(),
        ]);
    }

    /**
     * Review a report (POST)
     */
    public function reviewReport(int $id)
    {
        $this->requireModerator();

        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to('/moderation/reports/' . $id);
        }

        $action = $this->request->getPost('action');
        $notes = $this->request->getPost('notes');
        $actionTaken = $this->request->getPost('action_taken');

        if (!in_array($action, ['resolve', 'dismiss', 'escalate'])) {
            return redirect()->back()->with('error', 'Invalid action.');
        }

        $moderatorId = $this->getCurrentUserId();

        if ($action === 'resolve') {
            $result = $this->moderationService->resolveReport($id, $moderatorId, $actionTaken ?? 'reviewed', $notes ?? '');
        } elseif ($action === 'dismiss') {
            $result = $this->moderationService->dismissReport($id, $moderatorId, $notes ?? 'No action needed');
        } else {
            $result = $this->moderationService->escalateReport($id, $moderatorId);
        }

        if ($result) {
            return redirect()->to('/moderation/reports')->with('success', 'Report ' . $action . 'd successfully.');
        }

        return redirect()->back()->with('error', 'Could not process the report.');
    }

    /**
     * Moderation queue view
     */
    public function queue()
    {
        $this->requireModerator();

        $reports = $this->reportModel->getPendingReports(50);

        return view('moderation/queue', [
            'title' => 'Moderation Queue',
            'reports' => $reports,
        ]);
    }

    /**
     * View audit logs
     */
    public function auditLogs()
    {
        $this->requireModerator();

        $userId = $this->request->getGet('user_id');
        $action = $this->request->getGet('action');
        $entityType = $this->request->getGet('entity_type');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');

        $filters = [];
        if ($userId) {
            $filters['user_id'] = $userId;
        }
        if ($action) {
            $filters['action'] = $action;
        }
        if ($entityType) {
            $filters['entity_type'] = $entityType;
        }
        if ($dateFrom) {
            $filters['date_from'] = $dateFrom;
        }
        if ($dateTo) {
            $filters['date_to'] = $dateTo;
        }

        $logs = $this->auditLogService->getLogs($filters, 50);

        return view('moderation/audit_logs', [
            'title' => 'Audit Logs',
            'logs' => $logs,
            'filters' => [
                'user_id' => $userId,
                'action' => $action,
                'entity_type' => $entityType,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }

    /**
     * User management (admin only)
     */
    public function users()
    {
        $this->requireAdmin();

        $search = $this->request->getGet('search');
        $role = $this->request->getGet('role');

        $builder = $this->userModel->select('users.*, user_profiles.display_name')
            ->join('user_profiles', 'user_profiles.user_id = users.id', 'left');

        if ($search) {
            $builder->groupStart()
                ->like('users.username', $search)
                ->orLike('users.email', $search)
                ->orLike('user_profiles.display_name', $search)
                ->groupEnd();
        }

        if ($role) {
            $builder->where('users.role', $role);
        }

        $users = $builder->orderBy('users.created_at', 'DESC')
            ->orderBy('users.id', 'DESC')
            ->findAll(50);

        return view('moderation/users', [
            'title' => 'User Management',
            'users' => $users,
            'currentUserId' => $this->getCurrentUserId(),
            'filters' => [
                'search' => $search,
                'role' => $role,
            ],
        ]);
    }

    /**
     * Assign role to user (admin only, POST)
     */
    public function assignRole(int $userId)
    {
        $this->requireAdmin();

        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to('/moderation/users');
        }

        // Prevent admins from changing their own role
        $currentUserId = $this->getCurrentUserId();
        if ((int) $userId === (int) $currentUserId) {
            return redirect()->back()->with('error', 'You cannot change your own role.');
        }

        $role = $this->request->getPost('role');
        if (!in_array($role, ['admin', 'moderator', 'member'])) {
            return redirect()->back()->with('error', 'Invalid role.');
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $oldRole = $user['role'] ?? 'member';
        $result = $this->userModel->update($userId, ['role' => $role]);

        if ($result) {
            // Log the role change
            $this->auditLogService->log(
                'user.role_changed',
                'user',
                $userId,
                [
                    'old_role' => $oldRole,
                    'new_role' => $role,
                ],
                $currentUserId
            );

            // Preserve query parameters when redirecting back
            $queryParams = $this->request->getGet();
            $redirectUrl = '/moderation/users';
            if (!empty($queryParams)) {
                $redirectUrl .= '?' . http_build_query($queryParams);
            }
            return redirect()->to($redirectUrl)->with('success', 'User role updated successfully.');
        }

        $queryParams = $this->request->getGet();
        $redirectUrl = '/moderation/users';
        if (!empty($queryParams)) {
            $redirectUrl .= '?' . http_build_query($queryParams);
        }
        return redirect()->to($redirectUrl)->with('error', 'Could not update user role.');
    }

    /**
     * Toggle user status (enable/disable) - admin only, POST
     */
    public function toggleStatus(int $userId)
    {
        $this->requireAdmin();

        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to('/moderation/users');
        }

        // Prevent admins from disabling themselves
        $currentUserId = $this->getCurrentUserId();
        if ((int) $userId === (int) $currentUserId) {
            $queryParams = $this->request->getGet();
            $redirectUrl = '/moderation/users';
            if (!empty($queryParams)) {
                $redirectUrl .= '?' . http_build_query($queryParams);
            }
            return redirect()->to($redirectUrl)->with('error', 'You cannot disable your own account.');
        }

        $user = $this->userModel->find($userId);
        if (!$user) {
            $queryParams = $this->request->getGet();
            $redirectUrl = '/moderation/users';
            if (!empty($queryParams)) {
                $redirectUrl .= '?' . http_build_query($queryParams);
            }
            return redirect()->to($redirectUrl)->with('error', 'User not found.');
        }

        $currentStatus = $user['status'] ?? 'pending';
        $newStatus = ($currentStatus === 'active') ? 'suspended' : 'active';

        $result = $this->userModel->update($userId, ['status' => $newStatus]);

        if ($result) {
            // Log the status change
            $this->auditLogService->log(
                'user.status_changed',
                'user',
                $userId,
                [
                    'old_status' => $currentStatus,
                    'new_status' => $newStatus,
                ],
                $currentUserId
            );

            $message = $newStatus === 'active' ? 'User enabled successfully.' : 'User disabled successfully.';
            // Preserve query parameters when redirecting back
            $queryParams = $this->request->getGet();
            $redirectUrl = '/moderation/users';
            if (!empty($queryParams)) {
                $redirectUrl .= '?' . http_build_query($queryParams);
            }
            return redirect()->to($redirectUrl)->with('success', $message);
        }

        $queryParams = $this->request->getGet();
        $redirectUrl = '/moderation/users';
        if (!empty($queryParams)) {
            $redirectUrl .= '?' . http_build_query($queryParams);
        }
        return redirect()->to($redirectUrl)->with('error', 'Could not update user status.');
    }

    /**
     * List pending categories for moderation
     */
    public function categories()
    {
        $this->requireModerator();

        $categoryModel = model(CategoryModel::class);
        $status = $this->request->getGet('status') ?? 'pending';

        $categories = [];
        if ($status === 'pending') {
            $categories = $categoryModel->getPendingCategories();
        } else {
            $categories = $categoryModel->getCategoriesByStatus($status);
        }

        return view('moderation/categories', [
            'title' => 'Category Moderation',
            'categories' => $categories,
            'currentStatus' => $status,
        ]);
    }

    /**
     * Approve a pending category (POST)
     */
    public function approveCategory(int $id)
    {
        $this->requireModerator();

        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to('/moderation/categories');
        }

        $categoryModel = model(CategoryModel::class);
        $category = $categoryModel->find($id);

        if (!$category) {
            return redirect()->back()->with('error', 'Category not found.');
        }

        if ($category['status'] !== 'pending') {
            return redirect()->back()->with('error', 'Only pending categories can be approved.');
        }

        $currentUserId = $this->getCurrentUserId();
        $result = $categoryModel->update($id, [
            'status' => 'approved',
            'reviewed_by' => $currentUserId,
            'reviewed_at' => date('Y-m-d H:i:s'),
            'rejection_reason' => null,
        ]);

        if ($result) {
            // Log the approval
            $this->auditLogService->log(
                'category.approved',
                'category',
                $id,
                [
                    'name' => $category['name'],
                    'slug' => $category['slug'],
                ],
                $currentUserId
            );

            // Preserve query parameters when redirecting back
            $queryParams = $this->request->getGet();
            $redirectUrl = '/moderation/categories';
            if (!empty($queryParams)) {
                $redirectUrl .= '?' . http_build_query($queryParams);
            }
            return redirect()->to($redirectUrl)->with('success', 'Category approved successfully.');
        }

        return redirect()->back()->with('error', 'Could not approve category.');
    }

    /**
     * Reject a pending category (POST)
     */
    public function rejectCategory(int $id)
    {
        $this->requireModerator();

        if (strtolower($this->request->getMethod()) !== 'post') {
            return redirect()->to('/moderation/categories');
        }

        $categoryModel = model(CategoryModel::class);
        $category = $categoryModel->find($id);

        if (!$category) {
            return redirect()->back()->with('error', 'Category not found.');
        }

        if ($category['status'] !== 'pending') {
            return redirect()->back()->with('error', 'Only pending categories can be rejected.');
        }

        $rejectionReason = $this->request->getPost('rejection_reason');
        $currentUserId = $this->getCurrentUserId();

        $result = $categoryModel->update($id, [
            'status' => 'rejected',
            'reviewed_by' => $currentUserId,
            'reviewed_at' => date('Y-m-d H:i:s'),
            'rejection_reason' => $rejectionReason ? trim($rejectionReason) : null,
        ]);

        if ($result) {
            // Log the rejection
            $this->auditLogService->log(
                'category.rejected',
                'category',
                $id,
                [
                    'name' => $category['name'],
                    'slug' => $category['slug'],
                    'reason' => $rejectionReason,
                ],
                $currentUserId
            );

            // Preserve query parameters when redirecting back
            $queryParams = $this->request->getGet();
            $redirectUrl = '/moderation/categories';
            if (!empty($queryParams)) {
                $redirectUrl .= '?' . http_build_query($queryParams);
            }
            return redirect()->to($redirectUrl)->with('success', 'Category rejected successfully.');
        }

        return redirect()->back()->with('error', 'Could not reject category.');
    }
}

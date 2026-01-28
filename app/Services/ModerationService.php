<?php

namespace App\Services;

use App\Models\ReportModel;
use App\Models\ThreadModel;
use App\Models\PostModel;

class ModerationService
{
    protected $reportModel;
    protected $threadModel;
    protected $postModel;
    protected $auditLogService;

    public function __construct()
    {
        $this->reportModel = model(ReportModel::class);
        $this->threadModel = model(ThreadModel::class);
        $this->postModel = model(PostModel::class);
        $this->auditLogService = service('auditLog');
    }

    /**
     * Review a report and take action
     *
     * @param int $reportId
     * @param int $moderatorId
     * @param string $action Action to take: 'resolve', 'dismiss', 'escalate'
     * @param string|null $notes Resolution notes
     * @param string|null $actionTaken What action was taken (e.g., 'deleted', 'warned', 'no_action')
     * @return bool
     */
    public function reviewReport(int $reportId, int $moderatorId, string $action, ?string $notes = null, ?string $actionTaken = null): bool
    {
        $report = $this->reportModel->find($reportId);
        if (!$report) {
            return false;
        }

        $status = 'reviewed';
        if ($action === 'resolve') {
            $status = 'resolved';
        } elseif ($action === 'dismiss') {
            $status = 'dismissed';
        } elseif ($action === 'escalate') {
            $status = 'escalated';
        }

        $updateData = [
            'status' => $status,
            'reviewed_by' => $moderatorId,
            'reviewed_at' => date('Y-m-d H:i:s'),
            'resolution_notes' => $notes,
        ];

        $result = $this->reportModel->update($reportId, $updateData);

        if ($result) {
            // Log the action
            $this->auditLogService->log(
                'report.reviewed',
                'report',
                $reportId,
                [
                    'action' => $action,
                    'action_taken' => $actionTaken,
                    'previous_status' => $report['status'],
                    'new_status' => $status,
                ],
                $moderatorId
            );
        }

        return $result !== false;
    }

    /**
     * Get pending reports with optional filters
     *
     * @param array $filters
     * @param int $limit
     * @return array
     */
    public function getPendingReports(array $filters = [], int $limit = 50): array
    {
        return $this->reportModel->getReportsForModeration(array_merge(['status' => 'pending'], $filters), $limit);
    }

    /**
     * Get reports by status
     *
     * @param string $status
     * @param int $limit
     * @return array
     */
    public function getReportsByStatus(string $status, int $limit = 50): array
    {
        return $this->reportModel->getReportsForModeration(['status' => $status], $limit);
    }

    /**
     * Escalate a report to admin
     *
     * @param int $reportId
     * @param int $moderatorId
     * @return bool
     */
    public function escalateReport(int $reportId, int $moderatorId): bool
    {
        return $this->reviewReport($reportId, $moderatorId, 'escalate', 'Escalated to admin for review');
    }

    /**
     * Dismiss a report
     *
     * @param int $reportId
     * @param int $moderatorId
     * @param string $reason
     * @return bool
     */
    public function dismissReport(int $reportId, int $moderatorId, string $reason): bool
    {
        return $this->reviewReport($reportId, $moderatorId, 'dismiss', $reason, 'no_action');
    }

    /**
     * Resolve a report with action taken
     *
     * @param int $reportId
     * @param int $moderatorId
     * @param string $actionTaken What action was taken
     * @param string $notes Resolution notes
     * @return bool
     */
    public function resolveReport(int $reportId, int $moderatorId, string $actionTaken, string $notes): bool
    {
        return $this->reviewReport($reportId, $moderatorId, 'resolve', $notes, $actionTaken);
    }

    /**
     * Get report with related content
     *
     * @param int $reportId
     * @return array|null
     */
    public function getReportWithContent(int $reportId): ?array
    {
        $report = $this->reportModel->getReportWithContent($reportId);
        if (!$report) {
            return null;
        }

        // Load the actual content
        if ($report['content_type'] === 'thread') {
            $content = $this->threadModel->find($report['content_id']);
        } else {
            $content = $this->postModel->find($report['content_id']);
        }

        $report['content'] = $content;
        return $report;
    }
}

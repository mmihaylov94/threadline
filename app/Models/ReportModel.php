<?php

namespace App\Models;

use CodeIgniter\Model;

class ReportModel extends Model
{
    protected $table            = 'content_reports';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'reporter_id',
        'content_type',
        'content_id',
        'guideline_violations',
        'additional_notes',
        'status',
        'reviewed_by',
        'reviewed_at',
        'resolution_notes',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = null;

    protected $validationRules = [
        'reporter_id'   => 'required|is_natural_no_zero',
        'content_type'  => 'required|in_list[thread,post]',
        'content_id'    => 'required|is_natural_no_zero',
        'status'        => 'permit_empty|in_list[pending,reviewed,resolved,dismissed]',
    ];

    /**
     * Create a new report
     */
    public function createReport(array $data): int|false
    {
        if (!isset($data['status'])) {
            $data['status'] = 'pending';
        }

        // Convert guideline_violations array to JSON if it's an array
        if (isset($data['guideline_violations']) && is_array($data['guideline_violations'])) {
            $data['guideline_violations'] = json_encode($data['guideline_violations']);
        }

        return $this->insert($data);
    }

    /**
     * Get reports for specific content
     */
    public function getReportsByContent(string $type, int $contentId): array
    {
        return $this->select('content_reports.*, users.username as reporter_username')
            ->join('users', 'users.id = content_reports.reporter_id')
            ->where('content_reports.content_type', $type)
            ->where('content_reports.content_id', $contentId)
            ->orderBy('content_reports.created_at', 'DESC')
            ->findAll();
    }

    /**
     * Get pending reports for moderation queue
     */
    public function getPendingReports(int $limit = 50): array
    {
        return $this->select('content_reports.*, users.username as reporter_username')
            ->join('users', 'users.id = content_reports.reporter_id')
            ->where('content_reports.status', 'pending')
            ->orderBy('content_reports.created_at', 'ASC')
            ->findAll($limit, 0);
    }

    /**
     * Get reports for moderation with filters
     *
     * @param array $filters
     * @param int $limit
     * @return array
     */
    public function getReportsForModeration(array $filters = [], int $limit = 50): array
    {
        $builder = $this->select('content_reports.*, users.username as reporter_username, reviewers.username as reviewer_username')
            ->join('users', 'users.id = content_reports.reporter_id', 'left')
            ->join('users as reviewers', 'reviewers.id = content_reports.reviewed_by', 'left')
            ->orderBy('content_reports.created_at', 'DESC');

        if (isset($filters['status']) && $filters['status']) {
            $builder->where('content_reports.status', $filters['status']);
        }

        if (isset($filters['content_type']) && $filters['content_type']) {
            $builder->where('content_reports.content_type', $filters['content_type']);
        }

        if (isset($filters['date_from']) && $filters['date_from']) {
            $builder->where('content_reports.created_at >=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && $filters['date_to']) {
            $builder->where('content_reports.created_at <=', $filters['date_to']);
        }

        return $builder->findAll($limit, 0);
    }

    /**
     * Assign a reviewer to a report
     *
     * @param int $reportId
     * @param int $moderatorId
     * @return bool
     */
    public function assignReviewer(int $reportId, int $moderatorId): bool
    {
        return $this->update($reportId, [
            'reviewed_by' => $moderatorId,
        ]) !== false;
    }

    /**
     * Update report status
     *
     * @param int $reportId
     * @param string $status
     * @param int|null $moderatorId
     * @param string|null $notes
     * @return bool
     */
    public function updateStatus(int $reportId, string $status, ?int $moderatorId = null, ?string $notes = null): bool
    {
        $data = ['status' => $status];
        
        if ($moderatorId !== null) {
            $data['reviewed_by'] = $moderatorId;
            $data['reviewed_at'] = date('Y-m-d H:i:s');
        }
        
        if ($notes !== null) {
            $data['resolution_notes'] = $notes;
        }

        return $this->update($reportId, $data) !== false;
    }

    /**
     * Get report with related content data
     *
     * @param int $reportId
     * @return array|null
     */
    public function getReportWithContent(int $reportId): ?array
    {
        $report = $this->select('content_reports.*, users.username as reporter_username, reviewers.username as reviewer_username')
            ->join('users', 'users.id = content_reports.reporter_id', 'left')
            ->join('users as reviewers', 'reviewers.id = content_reports.reviewed_by', 'left')
            ->find($reportId);

        return $report;
    }
}

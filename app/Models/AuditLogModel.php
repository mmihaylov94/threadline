<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table            = 'audit_logs';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'changes',
        'ip_address',
        'user_agent',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';
    protected $deletedField  = '';

    /**
     * Get logs with optional filters
     *
     * @param array $filters
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getLogs(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $builder = $this->select('audit_logs.*, users.username as user_username')
            ->join('users', 'users.id = audit_logs.user_id', 'left')
            ->orderBy('audit_logs.created_at', 'DESC');

        if (isset($filters['user_id']) && $filters['user_id']) {
            $builder->where('audit_logs.user_id', $filters['user_id']);
        }

        if (isset($filters['action']) && $filters['action']) {
            $builder->where('audit_logs.action', $filters['action']);
        }

        if (isset($filters['entity_type']) && $filters['entity_type']) {
            $builder->where('audit_logs.entity_type', $filters['entity_type']);
        }

        if (isset($filters['entity_id']) && $filters['entity_id']) {
            $builder->where('audit_logs.entity_id', $filters['entity_id']);
        }

        if (isset($filters['date_from']) && $filters['date_from']) {
            $builder->where('audit_logs.created_at >=', $filters['date_from']);
        }

        if (isset($filters['date_to']) && $filters['date_to']) {
            $builder->where('audit_logs.created_at <=', $filters['date_to']);
        }

        return $builder->findAll($limit, $offset);
    }

    /**
     * Get logs for a specific user
     *
     * @param int $userId
     * @param int $limit
     * @return array
     */
    public function getUserLogs(int $userId, int $limit = 50): array
    {
        return $this->getLogs(['user_id' => $userId], $limit);
    }

    /**
     * Get logs for a specific entity
     *
     * @param string $entityType
     * @param int $entityId
     * @param int $limit
     * @return array
     */
    public function getEntityLogs(string $entityType, int $entityId, int $limit = 50): array
    {
        return $this->getLogs([
            'entity_type' => $entityType,
            'entity_id' => $entityId,
        ], $limit);
    }

    /**
     * Create a log entry
     *
     * @param array $data
     * @return int|false
     */
    public function createLog(array $data)
    {
        // Convert changes array to JSON if it's an array
        if (isset($data['changes']) && is_array($data['changes'])) {
            $data['changes'] = json_encode($data['changes']);
        }

        // CodeIgniter's insert() will automatically:
        // - Filter to only allowedFields
        // - Handle timestamps (created_at) via useTimestamps
        // - Skip validation if needed
        return $this->insert($data);
    }
}

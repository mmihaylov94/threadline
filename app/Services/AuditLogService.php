<?php

namespace App\Services;

use App\Models\AuditLogModel;
use CodeIgniter\HTTP\RequestInterface;

class AuditLogService
{
    protected $auditLogModel;
    protected $request;

    public function __construct()
    {
        $this->auditLogModel = model(AuditLogModel::class);
        $this->request = service('request');
    }

    /**
     * Log an action to the audit log
     *
     * @param string $action Action name (e.g., 'report.created', 'thread.deleted')
     * @param string $entityType Entity type (e.g., 'thread', 'post', 'report', 'user')
     * @param int|null $entityId Entity ID
     * @param array $changes Array of changes made
     * @param int|null $userId User ID performing the action (null for system actions)
     * @return int|false
     */
    public function log(string $action, string $entityType, ?int $entityId = null, array $changes = [], ?int $userId = null)
    {
        $userAgent = null;
        try {
            $ua = $this->request->getUserAgent();
            if ($ua) {
                $userAgent = $ua->getAgentString();
            }
        } catch (\Exception $e) {
            // User agent not available
        }

        $data = [
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'changes' => !empty($changes) ? $changes : null,
            'ip_address' => $this->request->getIPAddress() ?: null,
            'user_agent' => $userAgent,
        ];

        return $this->auditLogModel->createLog($data);
    }

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
        return $this->auditLogModel->getLogs($filters, $limit, $offset);
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
        return $this->auditLogModel->getUserLogs($userId, $limit);
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
        return $this->auditLogModel->getEntityLogs($entityType, $entityId, $limit);
    }
}

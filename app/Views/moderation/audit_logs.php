<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/moderation.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="moderation-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Audit Logs</h1>
        <a href="<?= base_url('moderation') ?>" class="btn btn-outline-secondary">Back to Dashboard</a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="<?= base_url('moderation/audit-logs') ?>">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="action" class="form-label">Action</label>
                        <input type="text" name="action" id="action" class="form-control" value="<?= esc($filters['action'] ?? '') ?>" placeholder="e.g., report.reviewed">
                    </div>
                    <div class="col-md-3">
                        <label for="entity_type" class="form-label">Entity Type</label>
                        <select name="entity_type" id="entity_type" class="form-select">
                            <option value="">All</option>
                            <option value="report" <?= $filters['entity_type'] === 'report' ? 'selected' : '' ?>>Report</option>
                            <option value="thread" <?= $filters['entity_type'] === 'thread' ? 'selected' : '' ?>>Thread</option>
                            <option value="post" <?= $filters['entity_type'] === 'post' ? 'selected' : '' ?>>Post</option>
                            <option value="user" <?= $filters['entity_type'] === 'user' ? 'selected' : '' ?>>User</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">From Date</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" value="<?= esc($filters['date_from'] ?? '') ?>">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">To Date</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" value="<?= esc($filters['date_to'] ?? '') ?>">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card">
        <div class="card-body">
            <?php if (empty($logs)): ?>
                <p class="text-muted mb-0">No audit logs found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Action</th>
                                <th>Entity</th>
                                <th>User</th>
                                <th>Changes</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($logs as $log): ?>
                                <tr>
                                    <td><?= $log['id'] ?></td>
                                    <td><code><?= esc($log['action']) ?></code></td>
                                    <td>
                                        <?= esc($log['entity_type']) ?>
                                        <?php if ($log['entity_id']): ?>
                                            #<?= $log['entity_id'] ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($log['user_username'] ?? 'System') ?></td>
                                    <td>
                                        <?php
                                        $changes = json_decode($log['changes'] ?? '{}', true);
                                        if (is_array($changes) && !empty($changes)):
                                            echo '<small><pre class="mb-0">' . esc(json_encode($changes, JSON_PRETTY_PRINT)) . '</pre></small>';
                                        else:
                                            echo '<span class="text-muted">-</span>';
                                        endif;
                                        ?>
                                    </td>
                                    <td><?= date('M j, Y g:i A', strtotime($log['created_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

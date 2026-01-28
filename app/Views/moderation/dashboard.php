<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/moderation.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="moderation-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Moderation Dashboard</h1>
        <div>
            <a href="<?= base_url('moderation/reports') ?>" class="btn btn-primary">View All Reports</a>
            <a href="<?= base_url('moderation/queue') ?>" class="btn btn-outline-primary">Moderation Queue</a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('success')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Pending Reports</h5>
                    <h2 class="mb-0"><?= $pendingCount ?></h2>
                    <a href="<?= base_url('moderation/queue') ?>" class="btn btn-sm btn-outline-primary mt-2">Review</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Pending Categories</h5>
                    <h2 class="mb-0"><?= $pendingCategoriesCount ?? 0 ?></h2>
                    <?php if (($pendingCategoriesCount ?? 0) > 0): ?>
                        <a href="<?= base_url('moderation/categories?status=pending') ?>" class="btn btn-sm btn-outline-primary mt-2">Review</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Reports Today</h5>
                    <h2 class="mb-0"><?= $reportsToday ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Resolved This Week</h5>
                    <h2 class="mb-0"><?= $resolvedThisWeek ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Quick Actions</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        <a href="<?= base_url('moderation/audit-logs') ?>" class="btn btn-sm btn-outline-secondary">Audit Logs</a>
                        <a href="<?= base_url('moderation/categories') ?>" class="btn btn-sm btn-outline-secondary">Category Moderation</a>
                        <?php if (isset($isAdmin) && $isAdmin): ?>
                            <a href="<?= base_url('moderation/users') ?>" class="btn btn-sm btn-outline-secondary">User Management</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Reports -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Pending Reports</h5>
            <a href="<?= base_url('moderation/reports?status=pending') ?>">View All</a>
        </div>
        <div class="card-body">
            <?php if (empty($pendingReports)): ?>
                <p class="text-muted mb-0">No pending reports.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Reporter</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pendingReports as $report): ?>
                                <tr>
                                    <td><?= $report['id'] ?></td>
                                    <td>
                                        <span class="badge bg-secondary"><?= esc(ucfirst($report['content_type'])) ?></span>
                                    </td>
                                    <td><?= esc($report['reporter_username'] ?? 'Unknown') ?></td>
                                    <td><?= date('M j, Y g:i A', strtotime($report['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= base_url('moderation/reports/' . $report['id']) ?>" class="btn btn-sm btn-primary">Review</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Audit Logs -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recent Activity</h5>
            <a href="<?= base_url('moderation/audit-logs') ?>">View All</a>
        </div>
        <div class="card-body">
            <?php if (empty($recentLogs)): ?>
                <p class="text-muted mb-0">No recent activity.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Entity</th>
                                <th>User</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentLogs as $log): ?>
                                <tr>
                                    <td><?= esc($log['action']) ?></td>
                                    <td>
                                        <?= esc($log['entity_type']) ?>
                                        <?php if ($log['entity_id']): ?>
                                            #<?= $log['entity_id'] ?>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($log['user_username'] ?? 'System') ?></td>
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

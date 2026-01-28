<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/moderation.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="moderation-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Reports</h1>
        <a href="<?= base_url('moderation') ?>" class="btn btn-outline-secondary">Back to Dashboard</a>
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

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="<?= base_url('moderation/reports') ?>">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All</option>
                            <option value="pending" <?= $filters['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="reviewed" <?= $filters['status'] === 'reviewed' ? 'selected' : '' ?>>Reviewed</option>
                            <option value="resolved" <?= $filters['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                            <option value="dismissed" <?= $filters['status'] === 'dismissed' ? 'selected' : '' ?>>Dismissed</option>
                            <option value="escalated" <?= $filters['status'] === 'escalated' ? 'selected' : '' ?>>Escalated</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="content_type" class="form-label">Content Type</label>
                        <select name="content_type" id="content_type" class="form-select">
                            <option value="">All</option>
                            <option value="thread" <?= $filters['content_type'] === 'thread' ? 'selected' : '' ?>>Thread</option>
                            <option value="post" <?= $filters['content_type'] === 'post' ? 'selected' : '' ?>>Post</option>
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

    <!-- Reports Table -->
    <div class="card">
        <div class="card-body">
            <?php if (empty($reports)): ?>
                <p class="text-muted mb-0">No reports found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Reporter</th>
                                <th>Status</th>
                                <th>Reviewed By</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reports as $report): ?>
                                <tr>
                                    <td><?= $report['id'] ?></td>
                                    <td>
                                        <span class="badge bg-secondary"><?= esc(ucfirst($report['content_type'])) ?></span>
                                    </td>
                                    <td><?= esc($report['reporter_username'] ?? 'Unknown') ?></td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'reviewed' => 'info',
                                            'resolved' => 'success',
                                            'dismissed' => 'secondary',
                                            'escalated' => 'danger',
                                        ];
                                        $color = $statusColors[$report['status']] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $color ?>"><?= esc(ucfirst($report['status'])) ?></span>
                                    </td>
                                    <td><?= esc($report['reviewer_username'] ?? '-') ?></td>
                                    <td><?= date('M j, Y g:i A', strtotime($report['created_at'])) ?></td>
                                    <td>
                                        <a href="<?= base_url('moderation/reports/' . $report['id']) ?>" class="btn btn-sm btn-primary">View</a>
                                    </td>
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

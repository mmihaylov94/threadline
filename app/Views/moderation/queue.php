<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/moderation.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="moderation-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Moderation Queue</h1>
        <a href="<?= base_url('moderation') ?>" class="btn btn-outline-secondary">Back to Dashboard</a>
    </div>

    <div class="card">
        <div class="card-body">
            <?php if (empty($reports)): ?>
                <p class="text-muted mb-0">No pending reports in the queue.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Type</th>
                                <th>Reporter</th>
                                <th>Violations</th>
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
                                        $violations = json_decode($report['guideline_violations'] ?? '[]', true);
                                        if (is_array($violations) && !empty($violations)):
                                            echo '<small>' . count($violations) . ' violation(s)</small>';
                                        else:
                                            echo '<small class="text-muted">None</small>';
                                        endif;
                                        ?>
                                    </td>
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
</div>

<?= $this->endSection() ?>

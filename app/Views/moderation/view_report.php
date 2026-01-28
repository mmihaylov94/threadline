<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/moderation.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="moderation-page">
    <div class="mb-4">
        <a href="<?= base_url('moderation/reports') ?>" class="btn btn-outline-secondary">← Back to Reports</a>
    </div>

    <h1>Report #<?= $report['id'] ?></h1>

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

    <div class="row">
        <div class="col-md-8">
            <!-- Report Details -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Report Details</h5>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">Status:</dt>
                        <dd class="col-sm-9">
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
                        </dd>

                        <dt class="col-sm-3">Content Type:</dt>
                        <dd class="col-sm-9"><?= esc(ucfirst($report['content_type'])) ?></dd>

                        <dt class="col-sm-3">Reporter:</dt>
                        <dd class="col-sm-9">
                            <a href="<?= base_url('users/' . $report['reporter_username']) ?>">
                                <?= esc($report['reporter_username']) ?>
                            </a>
                        </dd>

                        <dt class="col-sm-3">Created:</dt>
                        <dd class="col-sm-9"><?= date('M j, Y g:i A', strtotime($report['created_at'])) ?></dd>

                        <?php if ($report['reviewed_by']): ?>
                            <dt class="col-sm-3">Reviewed By:</dt>
                            <dd class="col-sm-9"><?= esc($report['reviewer_username'] ?? 'Unknown') ?></dd>

                            <dt class="col-sm-3">Reviewed At:</dt>
                            <dd class="col-sm-9"><?= date('M j, Y g:i A', strtotime($report['reviewed_at'])) ?></dd>
                        <?php endif; ?>
                    </dl>

                    <h6>Guideline Violations:</h6>
                    <?php
                    $violations = json_decode($report['guideline_violations'] ?? '[]', true);
                    if (is_array($violations) && !empty($violations)):
                    ?>
                        <ul>
                            <?php foreach ($violations as $violation): ?>
                                <li><?= esc(ucfirst(str_replace('_', ' ', $violation))) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted">No violations specified.</p>
                    <?php endif; ?>

                    <?php if ($report['additional_notes']): ?>
                        <h6>Additional Notes:</h6>
                        <p><?= esc($report['additional_notes']) ?></p>
                    <?php endif; ?>

                    <?php if ($report['resolution_notes']): ?>
                        <h6>Resolution Notes:</h6>
                        <p><?= esc($report['resolution_notes']) ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Content Preview -->
            <?php if ($content): ?>
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Reported Content</h5>
                        <?php if ($report['content_type'] === 'thread'): ?>
                            <a href="<?= base_url('threads/' . ($content['slug'] ?? '')) ?>" class="btn btn-sm btn-outline-primary" target="_blank">View Thread</a>
                        <?php else: ?>
                            <?php
                            $threadModel = model(ThreadModel::class);
                            $thread = $threadModel->find($content['thread_id']);
                            ?>
                            <a href="<?= base_url('threads/' . ($thread['slug'] ?? '') . '#post-' . $content['id']) ?>" class="btn btn-sm btn-outline-primary" target="_blank">View Post</a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if ($report['content_type'] === 'thread'): ?>
                            <h6><?= esc($content['title'] ?? 'N/A') ?></h6>
                            <div><?= $content['body'] ?? '' ?></div>
                        <?php else: ?>
                            <div><?= $content['body'] ?? '' ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-4">
            <!-- Actions -->
            <?php if ($report['status'] === 'pending' || $report['status'] === 'reviewed'): ?>
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Actions</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="<?= base_url('moderation/reports/' . $report['id'] . '/review') ?>">
                            <?= csrf_field() ?>
                            
                            <div class="mb-3">
                                <label for="action" class="form-label">Action</label>
                                <select name="action" id="action" class="form-select" required>
                                    <option value="">Select action...</option>
                                    <option value="resolve">Resolve</option>
                                    <option value="dismiss">Dismiss</option>
                                    <?php if (isset($isAdmin) && $isAdmin): ?>
                                        <option value="escalate">Escalate</option>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="mb-3" id="action-taken-group" style="display: none;">
                                <label for="action_taken" class="form-label">Action Taken</label>
                                <select name="action_taken" id="action_taken" class="form-select">
                                    <option value="reviewed">Reviewed</option>
                                    <option value="deleted">Content Deleted</option>
                                    <option value="warned">User Warned</option>
                                    <option value="no_action">No Action</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea name="notes" id="notes" class="form-control" rows="3" placeholder="Add resolution notes..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Submit</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Audit Log -->
            <?php if (!empty($auditLogs)): ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">History</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <?php foreach ($auditLogs as $log): ?>
                                <div class="list-group-item px-0">
                                    <small class="text-muted"><?= esc($log['action']) ?></small><br>
                                    <small><?= esc($log['user_username'] ?? 'System') ?> - <?= date('M j, Y g:i A', strtotime($log['created_at'])) ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
document.getElementById('action').addEventListener('change', function() {
    const actionTakenGroup = document.getElementById('action-taken-group');
    if (this.value === 'resolve') {
        actionTakenGroup.style.display = 'block';
        document.getElementById('action_taken').required = true;
    } else {
        actionTakenGroup.style.display = 'none';
        document.getElementById('action_taken').required = false;
    }
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>

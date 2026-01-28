<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/moderation.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="moderation-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Category Moderation</h1>
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

    <!-- Status Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="get" action="<?= base_url('moderation/categories') ?>">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="pending" <?= ($currentStatus ?? 'pending') === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="approved" <?= ($currentStatus ?? 'pending') === 'approved' ? 'selected' : '' ?>>Approved</option>
                            <option value="rejected" <?= ($currentStatus ?? 'pending') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="card">
        <div class="card-body">
            <?php if (empty($categories)): ?>
                <p class="text-muted mb-0">No <?= esc($currentStatus ?? 'pending') ?> categories found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Slug</th>
                                <th>Description</th>
                                <th>Created By</th>
                                <th>Created</th>
                                <?php if (($currentStatus ?? 'pending') === 'pending'): ?>
                                    <th>Actions</th>
                                <?php elseif (($currentStatus ?? 'pending') === 'rejected'): ?>
                                    <th>Rejection Reason</th>
                                <?php else: ?>
                                    <th>Reviewed By</th>
                                    <th>Reviewed At</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td><?= $category['id'] ?></td>
                                    <td><strong><?= esc($category['name']) ?></strong></td>
                                    <td><code><?= esc($category['slug']) ?></code></td>
                                    <td><?= esc($category['description'] ?? '-') ?></td>
                                    <td>
                                        <?php if (!empty($category['creator_username'])): ?>
                                            <a href="<?= base_url('users/' . esc($category['creator_username'])) ?>">
                                                <?= esc($category['creator_username']) ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">System</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('M j, Y g:i A', strtotime($category['created_at'])) ?></td>
                                    <?php if (($currentStatus ?? 'pending') === 'pending'): ?>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <form method="post" action="<?= base_url('moderation/categories/' . $category['id'] . '/approve') ?>" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm btn-success" 
                                                            onclick="return confirm('Are you sure you want to approve this category?');">
                                                        Approve
                                                    </button>
                                                </form>
                                                <button type="button" class="btn btn-sm btn-danger" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#rejectModal<?= $category['id'] ?>">
                                                    Reject
                                                </button>
                                            </div>
                                        </td>
                                    <?php elseif (($currentStatus ?? 'pending') === 'rejected'): ?>
                                        <td>
                                            <?php if (!empty($category['rejection_reason'])): ?>
                                                <span class="text-muted"><?= esc($category['rejection_reason']) ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">No reason provided</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php else: ?>
                                        <td>
                                            <?php if (!empty($category['reviewer_username'])): ?>
                                                <a href="<?= base_url('users/' . esc($category['reviewer_username'])) ?>">
                                                    <?= esc($category['reviewer_username']) ?>
                                                </a>
                                            <?php elseif (!empty($category['reviewed_by'])): ?>
                                                <span class="text-muted">User #<?= $category['reviewed_by'] ?></span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($category['reviewed_at'])): ?>
                                                <?= date('M j, Y g:i A', strtotime($category['reviewed_at'])) ?>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>

                                <!-- Reject Modal -->
                                <?php if (($currentStatus ?? 'pending') === 'pending'): ?>
                                    <div class="modal fade" id="rejectModal<?= $category['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form method="post" action="<?= base_url('moderation/categories/' . $category['id'] . '/reject') ?>">
                                                    <?= csrf_field() ?>
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Reject Category: <?= esc($category['name']) ?></h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="rejection_reason<?= $category['id'] ?>" class="form-label">Rejection Reason (Optional)</label>
                                                            <textarea name="rejection_reason" id="rejection_reason<?= $category['id'] ?>" class="form-control" rows="3" placeholder="Explain why this category is being rejected..."></textarea>
                                                            <small class="form-text text-muted">This reason will be visible to the category creator.</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Reject Category</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

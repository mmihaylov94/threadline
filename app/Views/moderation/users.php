<?= $this->extend('layout') ?>

<?= $this->section('styles') ?>
<link href="<?= base_url('assets/moderation.css') ?>" rel="stylesheet">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="moderation-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>User Management</h1>
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
            <form method="get" action="<?= base_url('moderation/users') ?>">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" name="search" id="search" class="form-control" value="<?= esc($filters['search'] ?? '') ?>" placeholder="Username, email, or display name">
                    </div>
                    <div class="col-md-4">
                        <label for="role" class="form-label">Role</label>
                        <select name="role" id="role" class="form-select">
                            <option value="">All</option>
                            <option value="admin" <?= $filters['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                            <option value="moderator" <?= $filters['role'] === 'moderator' ? 'selected' : '' ?>>Moderator</option>
                            <option value="member" <?= $filters['role'] === 'member' ? 'selected' : '' ?>>Member</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-body">
            <?php if (empty($users)): ?>
                <p class="text-muted mb-0">No users found.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Username</th>
                                <th>Display Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td>
                                        <a href="<?= base_url('users/' . $user['username']) ?>">
                                            <?= esc($user['username']) ?>
                                        </a>
                                    </td>
                                    <td><?= esc($user['display_name'] ?? '-') ?></td>
                                    <td><?= esc($user['email']) ?></td>
                                    <td>
                                        <?php
                                        $roleColors = [
                                            'admin' => 'danger',
                                            'moderator' => 'warning',
                                            'member' => 'secondary',
                                        ];
                                        $color = $roleColors[$user['role'] ?? 'member'] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $color ?>"><?= esc(ucfirst($user['role'] ?? 'member')) ?></span>
                                    </td>
                                    <td>
                                        <?php
                                        $statusColors = [
                                            'active' => 'success',
                                            'suspended' => 'warning',
                                            'banned' => 'danger',
                                            'pending' => 'secondary',
                                        ];
                                        $statusColor = $statusColors[$user['status'] ?? 'pending'] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $statusColor ?>"><?= esc(ucfirst($user['status'] ?? 'pending')) ?></span>
                                    </td>
                                    <td><?= date('M j, Y', strtotime($user['created_at'])) ?></td>
                                    <td>
                                        <?php 
                                        $isCurrentUser = isset($currentUserId) && (int) $user['id'] === (int) $currentUserId;
                                        $isActive = ($user['status'] ?? 'pending') === 'active';
                                        ?>
                                        <?php if (!$isCurrentUser): ?>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#roleModal<?= $user['id'] ?>">
                                                    Change Role
                                                </button>
                                                <form method="post" action="<?= base_url('moderation/users/' . $user['id'] . '/status') ?>" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <button type="submit" class="btn btn-sm <?= $isActive ? 'btn-outline-warning' : 'btn-outline-success' ?>" 
                                                            onclick="return confirm('Are you sure you want to <?= $isActive ? 'disable' : 'enable' ?> this user?');">
                                                        <?= $isActive ? 'Disable' : 'Enable' ?>
                                                    </button>
                                                </form>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted small">You</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                                <!-- Role Change Modal -->
                                <div class="modal fade" id="roleModal<?= $user['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="post" action="<?= base_url('moderation/users/' . $user['id'] . '/role') ?>">
                                                <?= csrf_field() ?>
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Change Role for <?= esc($user['username']) ?></h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="role<?= $user['id'] ?>" class="form-label">Role</label>
                                                        <select name="role" id="role<?= $user['id'] ?>" class="form-select" required>
                                                            <option value="member" <?= ($user['role'] ?? 'member') === 'member' ? 'selected' : '' ?>>Member</option>
                                                            <option value="moderator" <?= ($user['role'] ?? 'member') === 'moderator' ? 'selected' : '' ?>>Moderator</option>
                                                            <option value="admin" <?= ($user['role'] ?? 'member') === 'admin' ? 'selected' : '' ?>>Admin</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-primary">Update Role</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

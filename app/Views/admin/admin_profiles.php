<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <p class="text-muted">Directory of administrator accounts. Each administrator can update their own profile from My Profile.</p>

    <div class="row g-3">
        <?php if (empty($admins)): ?>
            <div class="col-12">
                <div class="card portal-card"><div class="card-body text-muted">No administrator profiles found.</div></div>
            </div>
        <?php else: ?>
            <?php foreach ($admins as $admin): ?>
                <div class="col-md-6 col-xl-4">
                    <article class="card portal-card h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                                <h5 class="card-title mb-0"><?= esc($admin['full_name']) ?></h5>
                                <span class="badge <?= (int) ($admin['is_active'] ?? 1) === 1 ? 'text-bg-success' : 'text-bg-secondary' ?>">
                                    <?= (int) ($admin['is_active'] ?? 1) === 1 ? 'Active' : 'Inactive' ?>
                                </span>
                            </div>
                            <dl class="mb-0">
                                <dt class="small text-muted">Employee ID</dt>
                                <dd><?= esc($admin['employee_id'] ?? '—') ?></dd>
                                <dt class="small text-muted">Username</dt>
                                <dd><?= esc($admin['username'] ?? '—') ?></dd>
                                <dt class="small text-muted">Account Created</dt>
                                <dd class="mb-0"><?= !empty($admin['created_at']) ? esc(date('M j, Y', strtotime($admin['created_at']))) : '—' ?></dd>
                            </dl>
                        </div>
                        <?php if ((int) $admin['id'] === (int) session()->get('user_id')): ?>
                            <div class="card-footer bg-white">
                                <a href="<?= site_url('admin/profile') ?>" class="btn btn-sm btn-outline-university">Edit My Profile</a>
                            </div>
                        <?php endif; ?>
                    </article>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?= view('layouts/footer') ?>

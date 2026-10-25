<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <p class="text-muted mb-2">Manage teacher accounts. Add student, teacher, and admin accounts from Add Account.</p>
    <div class="student-directory-toolbar mb-3">
        <form method="GET" class="student-directory-filters">
            <input type="text" name="q" class="form-control form-control-sm admin-user-search" placeholder="Search name, username, college, or date (e.g. Aug or August 9, 2026)" value="<?= esc($q ?? '') ?>">
            <button type="submit" class="btn btn-sm btn-university">Search</button>
            <a href="<?= site_url('admin/users') ?>" class="btn btn-sm btn-university clear-filter-button">Clear</a>
        </form>
        <div class="student-directory-actions">
            <a href="<?= site_url('admin/add-account') ?>" class="btn btn-sm btn-university">Add Account</a>
            <a href="<?= site_url('admin/users/print') . '?' . http_build_query(array_filter(['q' => $q], static fn ($value): bool => $value !== '')) ?>" target="_blank" class="btn btn-sm btn-university">Print Directory</a>
        </div>
    </div>

    <?php if ($saved): ?><div class="alert alert-success py-2"><?= esc($saved) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger py-2"><?= esc($error) ?></div><?php endif; ?>

    <div class="card portal-card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Teacher Directory</h5>
            <span class="small text-muted"><?= count($users) ?> teacher<?= count($users) === 1 ? '' : 's' ?></span>
        </div>
        <div class="card-body p-0">
            <table class="table table-portal table-striped mb-0">
                <thead>
                    <tr><th>Employee ID</th><th>Full Name</th><th>Username</th><th>College / Office</th><th>Created</th><th>Status</th><th class="directory-actions-heading">Actions</th></tr>
                </thead>
                <tbody>
                <?php if (empty($users)): ?>
                    <tr><td colspan="7" class="text-muted">No teachers found.</td></tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                    <?php $isActive = (int) ($u['is_active'] ?? 1) === 1; ?>
                    <tr>
                        <td><?= esc($u['employee_id'] ?? '—') ?></td>
                        <td><?= esc($u['full_name']) ?></td>
                        <td><?= esc($u['username'] ?? '') ?></td>
                        <td><?= esc($u['college'] ?? '') ?></td>
                        <td class="small text-muted"><?= !empty($u['created_at']) ? esc(date('M j, Y', strtotime($u['created_at']))) : '—' ?></td>
                        <td><span class="badge <?= $isActive ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= $isActive ? 'Active' : 'Inactive' ?></span></td>
                        <td class="directory-actions-cell">
                            <div class="directory-row-actions">
                            <a href="<?= site_url('admin/users/' . $u['id'] . '/edit') ?>" class="btn btn-sm btn-outline-university directory-edit-button">Edit</a>
                            <button
                                type="button"
                                class="btn btn-sm <?= $isActive ? 'btn-outline-danger' : 'btn-outline-success' ?> directory-status-button"
                                data-bs-toggle="modal"
                                data-bs-target="#accountStatusModal"
                                data-user-name="<?= esc($u['full_name'], 'attr') ?>"
                                data-action="<?= site_url('admin/users/' . $u['id'] . '/' . ($isActive ? 'deactivate' : 'activate')) ?>"
                                data-next-status="<?= $isActive ? 'deactivate' : 'reactivate' ?>"
                            ><?= $isActive ? 'Deactivate' : 'Reactivate' ?></button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= view('admin/account_status_modal') ?>
<?= view('layouts/footer') ?>

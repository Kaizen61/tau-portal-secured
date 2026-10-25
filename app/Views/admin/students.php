<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Students</li>
        </ol>
    </nav>

    <p class="text-muted mb-2">Manage student accounts. Create accounts from <a href="<?= site_url('admin/add-account') ?>">Add Account</a>.</p>
    <div class="student-directory-toolbar mb-3">
        <form method="GET" class="student-directory-filters">
            <input type="text" name="q" class="form-control form-control-sm student-search-input" placeholder="Search name, student no., college, year level, or date" value="<?= esc($q ?? '') ?>">
            <select name="sort" class="form-select form-select-sm student-sort-select" aria-label="Sort by name">
                <option value="asc" <?= $sort === 'asc' ? 'selected' : '' ?>>Name: A to Z</option>
                <option value="desc" <?= $sort === 'desc' ? 'selected' : '' ?>>Name: Z to A</option>
            </select>
            <select name="college" class="form-select form-select-sm student-college-select" aria-label="Filter by college">
                <option value="">All Colleges</option>
                <?php foreach ($colleges as $college): ?>
                    <option value="<?= esc($college) ?>" <?= $collegeFilter === $college ? 'selected' : '' ?>><?= esc($college) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="year_level" class="form-select form-select-sm student-year-select" aria-label="Filter by year level">
                <option value="">All Year Levels</option>
                <?php foreach ($yearLevels as $yearLevel): ?>
                    <option value="<?= esc($yearLevel) ?>" <?= $yearLevelFilter === $yearLevel ? 'selected' : '' ?>><?= esc($yearLevel) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-sm btn-university">Search</button>
            <a href="<?= site_url('admin/students') ?>" class="btn btn-sm btn-university clear-filter-button">Clear</a>
        </form>
        <div class="student-directory-actions">
            <a href="<?= site_url('admin/add-account') ?>" class="btn btn-sm btn-university">Add Account</a>
            <a href="<?= site_url('admin/students/print') . '?' . http_build_query(array_filter(['q' => $q, 'sort' => $sort, 'college' => $collegeFilter, 'year_level' => $yearLevelFilter], static fn ($value): bool => $value !== '')) ?>" target="_blank" class="btn btn-sm btn-university">Print Directory</a>
        </div>
    </div>

    <?php if ($saved): ?><div class="alert alert-success py-2"><?= esc($saved) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger py-2"><?= esc($error) ?></div><?php endif; ?>

    <div class="card portal-card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Student Directory</h5>
            <span class="small text-muted"><?= count($students) ?> student<?= count($students) === 1 ? '' : 's' ?></span>
        </div>
        <div class="card-body p-0 student-directory-scroll-area" aria-label="Student directory list; scroll to view additional students">
            <table class="table table-portal table-striped mb-0">
                <thead>
                    <tr>
                        <th>Student Number</th>
                        <th>Full Name</th>
                        <th>College</th>
                        <th>Program</th>
                        <th>Year Level</th>
                        <th>Section</th>
                        <th>Created</th>
                        <th>Status</th>
                        <th class="directory-actions-heading">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($students)): ?>
                    <tr><td colspan="9" class="text-muted">No students found.</td></tr>
                <?php else: ?>
                    <?php foreach ($students as $s): ?>
                    <?php $isActive = (int) ($s['is_active'] ?? 1) === 1; ?>
                    <tr>
                        <td><?= esc($s['student_number'] ?? '') ?></td>
                        <td><?= esc($s['full_name']) ?></td>
                        <td><?= esc($s['college'] ?? '') ?></td>
                        <td title="<?= esc($programNames[$s['program'] ?? ''] ?? '', 'attr') ?>"><?= esc($s['program'] ?? '—') ?></td>
                        <td><?= esc($s['year_level'] ?? '') ?></td>
                        <td><?= esc($s['section'] ?? '—') ?></td>
                        <td class="small text-muted"><?= !empty($s['created_at']) ? esc(date('M j, Y', strtotime($s['created_at']))) : '—' ?></td>
                        <td><span class="badge <?= $isActive ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= $isActive ? 'Active' : 'Inactive' ?></span></td>
                        <td class="directory-actions-cell">
                            <div class="directory-row-actions">
                            <a href="<?= site_url('admin/users/' . $s['id'] . '/edit') ?>" class="btn btn-sm btn-outline-university directory-edit-button">Edit</a>
                            <button
                                type="button"
                                class="btn btn-sm <?= $isActive ? 'btn-outline-danger' : 'btn-outline-success' ?> directory-status-button"
                                data-bs-toggle="modal"
                                data-bs-target="#accountStatusModal"
                                data-user-name="<?= esc($s['full_name'], 'attr') ?>"
                                data-action="<?= site_url('admin/users/' . $s['id'] . '/' . ($isActive ? 'deactivate' : 'activate')) ?>"
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

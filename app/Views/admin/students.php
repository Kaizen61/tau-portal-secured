<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active">Students</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <p class="text-muted mb-0">All student accounts. Create new accounts from Manage Users.</p>
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="q" class="form-control form-control-sm" style="min-width:220px;" placeholder="Search name or student no." value="<?= esc($q ?? '') ?>">
            <button type="submit" class="btn btn-sm btn-university">Search</button>
            <?php if (!empty($q)): ?>
                <a href="<?= site_url('admin/students') ?>" class="btn btn-sm btn-outline-university">Clear</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="card portal-card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Student Directory</h5>
            <span class="small text-muted"><?= count($students) ?> student<?= count($students) === 1 ? '' : 's' ?></span>
        </div>
        <div class="card-body p-0">
            <table class="table table-portal table-striped mb-0">
                <thead>
                    <tr>
                        <th>Student Number</th>
                        <th>Full Name</th>
                        <th>College</th>
                        <th>Year Level</th>
                        <th>Created</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($students)): ?>
                    <tr><td colspan="5" class="text-muted">No students found.</td></tr>
                <?php else: ?>
                    <?php foreach ($students as $s): ?>
                    <tr>
                        <td><?= esc($s['student_number'] ?? '') ?></td>
                        <td><?= esc($s['full_name']) ?></td>
                        <td><?= esc($s['college'] ?? '') ?></td>
                        <td><?= esc($s['year_level'] ?? '') ?></td>
                        <td class="small text-muted"><?= !empty($s['created_at']) ? esc(date('M j, Y', strtotime($s['created_at']))) : '—' ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= view('layouts/footer') ?>

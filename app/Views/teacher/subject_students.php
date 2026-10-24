<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= site_url('teacher/dashboard') ?>">Faculty Dashboard</a></li>
            <li class="breadcrumb-item active"><?= esc($subject['subject_name']) ?></li>
        </ol>
    </nav>
    <p class="text-muted"><?= esc($subject['units']) ?> units</p>

    <?php if ($saved): ?><div class="alert alert-success py-2"><?= esc($saved) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger py-2"><?= esc($error) ?></div><?php endif; ?>

    <div class="card portal-card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0">Class List</h5>
            <div class="d-flex flex-wrap gap-2 no-print">
                <a class="btn btn-sm btn-university class-list-print-button" target="_blank" href="<?= site_url('teacher/subjects/' . $subject['id'] . '/print') ?>">Print with Grades</a>
                <a class="btn btn-sm btn-outline-university class-list-print-button" target="_blank" href="<?= site_url('teacher/subjects/' . $subject['id'] . '/print?include_grades=0') ?>">Print without Grades</a>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-portal table-striped mb-0 align-middle">
                <thead><tr><th>Student</th><th>Year Level</th><th>Section</th><th>School Year</th><th>Grade</th><th style="width:220px;">Update Grade</th></tr></thead>
                <tbody>
                <?php foreach ($students as $row): ?>
                    <tr>
                        <td><?= esc($row['full_name']) ?></td>
                        <td><?= esc($row['year_level'] ?? '') ?></td>
                        <td><?= esc($row['section'] ?? '—') ?></td>
                        <td><?= esc($row['school_year']) ?></td>
                        <td>
                            <?php if ($row['grade'] === null): ?>
                                <span class="badge bg-secondary">Not yet graded</span>
                            <?php else: ?><?= esc($row['grade']) ?><?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" action="<?= site_url('teacher/subjects/' . $subject['id'] . '/grade') ?>" class="d-flex gap-2">
                                <?= csrf_field() ?>
                                <input type="hidden" name="grade_row_id" value="<?= esc($row['grade_row_id']) ?>">
                                <input type="text" name="grade" class="form-control form-control-sm" placeholder="1.00-5.00">
                                <button type="submit" class="btn btn-sm btn-university">Save</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= view('layouts/footer') ?>

<?php $yearLabels = [1 => '1st Year', 2 => '2nd Year', 3 => '3rd Year', 4 => '4th Year']; ?>
<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <p class="text-muted mb-0">Subjects you are currently enrolled in.</p>
        <form method="GET" class="d-flex gap-2">
            <select name="school_year" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All School Years</option>
                <?php foreach ($schoolYears as $sy): ?>
                    <option value="<?= esc($sy['school_year']) ?>" <?= $filterSy === $sy['school_year'] ? 'selected' : '' ?>><?= esc($sy['school_year']) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="semester" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Semesters</option>
                <option value="1st" <?= $filterSem === '1st' ? 'selected' : '' ?>>1st Semester</option>
                <option value="2nd" <?= $filterSem === '2nd' ? 'selected' : '' ?>>2nd Semester</option>
            </select>
        </form>
    </div>

    <div class="row g-3">
        <?php foreach ($courses as $row): ?>
        <div class="col-md-6">
            <div class="card portal-card h-100"><div class="card-body">
                <h6 class="text-muted mb-1"><?= esc($row['subject_code']) ?> &middot; <?= esc($row['units']) ?> units</h6>
                <h5 class="card-title"><?= esc($row['subject_name']) ?></h5>
                <p class="small text-muted mb-1"><?= esc($yearLabels[$row['year_level']] ?? '') ?> &middot; <?= esc($row['semester']) ?> Semester &middot; <?= esc($row['school_year']) ?></p>
                <p class="small text-muted mb-2">Instructor: <?= esc($row['teacher_name'] ?? 'Unassigned') ?></p>
                <div class="border-top pt-2 mt-2">
                    <span class="small text-muted">Course materials: </span>
                    <span class="badge bg-secondary">No materials uploaded yet</span>
                </div>
            </div></div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?= view('layouts/footer') ?>

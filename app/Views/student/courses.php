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
            <a href="<?= site_url('student/courses') ?>" class="btn btn-sm btn-outline-university clear-filter-button">Clear</a>
        </form>
    </div>

    <?php if (empty($groups)): ?>
        <div class="card portal-card"><div class="card-body text-muted">No enrolled subjects found for this filter.</div></div>
    <?php endif; ?>

    <?php foreach ($groups as $group): ?>
    <section class="card portal-card course-semester-group mb-4" aria-label="<?= esc(($yearLabels[$group['year_level']] ?? '') . ' ' . $group['semester'] . ' Semester, School Year ' . $group['school_year']) ?>">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2 px-3 py-3">
            <div>
                <h5 class="mb-1"><?= esc($yearLabels[$group['year_level']] ?? '') ?> &middot; <?= esc($group['semester']) ?> Semester</h5>
                <span class="text-muted small">School Year <?= esc($group['school_year']) ?> <span class="mx-1">&middot;</span> <?= (int) $group['total_units'] ?> units</span>
            </div>
            <a class="btn btn-sm btn-outline-university no-print" target="_blank"
               href="<?= site_url('student/courses/print') ?>?school_year=<?= urlencode($group['school_year']) ?>&semester=<?= urlencode($group['semester']) ?>">
                Print Certificate of Registration
            </a>
        </div>
        <div class="card-body p-3">
        <div class="row g-3">
            <?php foreach ($group['subjects'] as $row): ?>
            <div class="col-md-6">
                <div class="card portal-card h-100"><div class="card-body">
                    <h6 class="text-muted mb-1"><?= esc($row['subject_code']) ?> &middot; <?= esc($row['units']) ?> units</h6>
                    <h5 class="card-title"><?= esc($row['subject_name']) ?></h5>
                    <p class="small text-muted mb-1"><?= esc($yearLabels[$row['year_level']] ?? '') ?> &middot; <?= esc($row['semester']) ?> Semester &middot; <?= esc($row['school_year']) ?></p>
                    <p class="small text-muted mb-2">Instructor: <?= esc($row['teacher_name'] ?? 'Unassigned') ?></p>
                </div></div>
            </div>
            <?php endforeach; ?>
        </div>
        </div>
    </section>
    <?php endforeach; ?>
</div>

<?= view('layouts/footer') ?>

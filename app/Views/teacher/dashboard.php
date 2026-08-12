<?php $yearLabels = [1 => '1st Year', 2 => '2nd Year', 3 => '3rd Year', 4 => '4th Year']; ?>
<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <p class="text-muted mb-0">Welcome, <?= esc(session()->get('full_name')) ?>. Manage grades for your assigned subjects below.</p>
        <form method="GET" class="d-flex gap-2">
            <select name="year_level" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Year Levels</option>
                <?php foreach ($yearLabels as $val => $label): ?>
                    <option value="<?= $val ?>" <?= $filterYear == $val ? 'selected' : '' ?>><?= $label ?></option>
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
        <?php foreach ($subjectList as $s): ?>
        <div class="col-md-4">
            <div class="card portal-card h-100"><div class="card-body">
                <h6 class="text-muted mb-1"><?= esc($s['subject_code']) ?> &middot; <?= esc($s['units']) ?> units</h6>
                <h5 class="card-title"><?= esc($s['subject_name']) ?></h5>
                <p class="small text-muted mb-2"><?= esc($yearLabels[$s['year_level']] ?? '') ?> &middot; <?= esc($s['semester']) ?> Semester</p>
                <p class="card-text small text-muted"><?= (int)$s['student_count'] ?> student(s) enrolled</p>
                <a href="<?= site_url('teacher/subjects/' . $s['id']) ?>" class="btn btn-university btn-sm">View Class List</a>
            </div></div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?= view('layouts/footer') ?>

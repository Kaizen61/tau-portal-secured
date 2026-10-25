<?php
$yearLabels = [1 => '1st Year', 2 => '2nd Year', 3 => '3rd Year', 4 => '4th Year'];
?>
<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <p class="text-muted">Create subjects, assign faculty, and enroll students.</p>

    <?php if ($saved): ?><div class="alert alert-success py-2"><?= esc($saved) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger py-2"><?= esc($error) ?></div><?php endif; ?>

    <div class="card portal-card mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0">All Subjects</h5>
            <form method="GET" class="d-flex gap-2">
                <input type="search" name="q" class="form-control form-control-sm" placeholder="Search code, subject, or faculty" value="<?= esc($q ?? '') ?>">
                <select name="year_level" class="form-select form-select-sm" data-submit-on-change>
                    <option value="">All Year Levels</option>
                    <?php foreach ($yearLabels as $val => $label): ?>
                        <option value="<?= $val ?>" <?= $filterYear == $val ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="semester" class="form-select form-select-sm" data-submit-on-change>
                    <option value="">All Semesters</option>
                    <option value="1st" <?= $filterSem === '1st' ? 'selected' : '' ?>>1st Semester</option>
                    <option value="2nd" <?= $filterSem === '2nd' ? 'selected' : '' ?>>2nd Semester</option>
                </select>
                <a href="<?= site_url('admin/subjects') ?>" class="btn btn-sm btn-outline-university clear-filter-button">Clear</a>
            </form>
        </div>
        <div class="card-body p-0 admin-subject-list">
            <table class="table table-portal table-striped mb-0">
                <thead><tr><th>Code</th><th>Subject</th><th>Units</th><th>Year / Semester</th><th>Faculty</th><th>Action</th></tr></thead>
                <tbody>
                <?php if (empty($subjectList)): ?>
                    <tr><td colspan="6" class="text-muted">No subjects found.</td></tr>
                <?php endif; ?>
                <?php foreach ($subjectList as $s): ?>
                    <tr>
                        <td><?= esc($s['subject_code']) ?></td>
                        <td><?= esc($s['subject_name']) ?></td>
                        <td><?= esc($s['units']) ?></td>
                        <td><?= esc($yearLabels[$s['year_level']] ?? $s['year_level']) ?> &middot; <?= esc($s['semester']) ?> Sem</td>
                        <td><?= esc($s['teacher_name'] ?? 'Unassigned') ?></td>
                        <td><a href="<?= site_url('admin/subjects/' . $s['id'] . '/edit') ?>" class="btn btn-sm btn-outline-university">Edit</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card portal-card">
                <div class="card-header bg-white"><h5 class="mb-0">Add New Subject</h5></div>
                <div class="card-body">
                    <form method="POST" action="<?= site_url('admin/subjects') ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3"><label class="form-label">Subject Code</label>
                            <input type="text" name="subject_code" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Subject Name</label>
                            <input type="text" name="subject_name" class="form-control" required></div>
                        <div class="row">
                            <div class="col-6 mb-3"><label class="form-label">Units</label>
                                <input type="number" name="units" class="form-control" value="3" min="1" max="99" required></div>
                            <div class="col-6 mb-3"><label class="form-label">Year Level</label>
                                <select name="year_level" class="form-select" required>
                                    <option value="">Select year level</option>
                                    <?php foreach ($yearLabels as $val => $label): ?><option value="<?= $val ?>"><?= $label ?></option><?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3"><label class="form-label">Semester</label>
                            <select name="semester" class="form-select" required>
                                <option value="">Select semester</option>
                                <option value="1st">1st Semester</option>
                                <option value="2nd">2nd Semester</option>
                            </select>
                        </div>
                        <div class="mb-3"><label class="form-label">Assign Faculty</label>
                            <select name="teacher_id" class="form-select" required>
                                <option value="">Select faculty</option>
                                <?php foreach ($teachers as $t): ?><option value="<?= esc($t['id']) ?>"><?= esc($t['full_name']) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-university w-100">Add Subject</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card portal-card">
                <div class="card-header bg-white"><h5 class="mb-0">Enroll Student in Subject</h5></div>
                <div class="card-body">
                    <form method="POST" action="<?= site_url('admin/subjects/enroll') ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3"><label class="form-label">Student</label>
                            <select name="student_id" class="form-select" required>
                                <option value="">Select student</option>
                                <?php foreach ($students as $st): ?><option value="<?= esc($st['id']) ?>"><?= esc($st['full_name']) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3"><label class="form-label">Subject</label>
                            <select name="subject_id" class="form-select" required>
                                <option value="">Select subject</option>
                                <?php foreach ($allSubjects as $sub): ?>
                                    <option value="<?= esc($sub['id']) ?>"><?= esc($sub['subject_code'] . ' - ' . $sub['subject_name']) ?> (<?= esc($yearLabels[$sub['year_level']] ?? '') ?>, <?= esc($sub['semester']) ?> Sem)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">School Year</label>
                            <input type="hidden" name="school_year" value="<?= esc($currentSchoolYear, 'attr') ?>" required data-school-year-input>
                            <button type="button" class="form-select text-start" data-bs-toggle="modal" data-bs-target="#schoolYearModal" data-school-year-display>
                                <?= esc($currentSchoolYear) ?>
                            </button>
                        </div>
                        <button type="submit" class="btn btn-university w-100">Enroll Student</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('components/school_year_modal', ['schoolYears' => $schoolYears]) ?>
<?= view('layouts/footer') ?>

<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= site_url('teacher/dashboard') ?>">Faculty Dashboard</a></li>
            <li class="breadcrumb-item active">Input Grade</li>
        </ol>
    </nav>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card portal-card">
                <div class="card-header bg-white"><h5 class="mb-0">Input a New Grade Record</h5></div>
                <div class="card-body">
                    <?php if ($saved): ?><div class="alert alert-success py-2"><?= esc($saved) ?></div><?php endif; ?>
                    <?php if ($error): ?><div class="alert alert-danger py-2"><?= esc($error) ?></div><?php endif; ?>

                    <form method="POST" action="<?= site_url('teacher/grade-input') ?>">
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
                                <?php foreach ($subjects as $sub): ?><option value="<?= esc($sub['id']) ?>"><?= esc($sub['subject_code'] . ' - ' . $sub['subject_name']) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">School Year</label>
                            <input type="hidden" name="school_year" value="<?= esc($currentSchoolYear, 'attr') ?>" required data-school-year-input>
                            <button type="button" class="form-select text-start" data-bs-toggle="modal" data-bs-target="#schoolYearModal" data-school-year-display>
                                <?= esc($currentSchoolYear) ?>
                            </button>
                        </div>
                        <div class="mb-3"><label class="form-label">Grade</label>
                            <input type="text" name="grade" class="form-control" placeholder="1.00-5.00"></div>
                        <button type="submit" class="btn btn-university">Save Grade</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('components/school_year_modal', ['schoolYears' => $schoolYears]) ?>
<?= view('layouts/footer') ?>

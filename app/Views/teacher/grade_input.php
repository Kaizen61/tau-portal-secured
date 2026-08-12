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
                            <select name="student_id" class="form-select">
                                <?php foreach ($students as $st): ?><option value="<?= esc($st['id']) ?>"><?= esc($st['full_name']) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3"><label class="form-label">Subject</label>
                            <select name="subject_id" class="form-select">
                                <?php foreach ($subjects as $sub): ?><option value="<?= esc($sub['id']) ?>"><?= esc($sub['subject_code'] . ' - ' . $sub['subject_name']) ?></option><?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3"><label class="form-label">School Year</label>
                            <input type="text" name="school_year" class="form-control" value="2025-2026"></div>
                        <div class="mb-3"><label class="form-label">Grade</label>
                            <input type="text" name="grade" class="form-control" placeholder="1.00-5.00"></div>
                        <button type="submit" class="btn btn-university">Save Grade</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('layouts/footer') ?>

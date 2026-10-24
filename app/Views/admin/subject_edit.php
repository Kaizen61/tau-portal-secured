<?= view('layouts/header', get_defined_vars()) ?>

<div class="row">
    <div class="col-lg-8 col-xl-6">
        <div class="card portal-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit Subject</h5>
                <a href="<?= site_url('admin/subjects') ?>" class="btn btn-sm btn-outline-university">Back to Subjects</a>
            </div>
            <div class="card-body">
                <?php if ($error): ?><div class="alert alert-danger py-2"><?= esc($error) ?></div><?php endif; ?>
                <form method="POST" action="<?= site_url('admin/subjects/' . $subject['id']) ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label" for="subjectCode">Subject Code</label>
                        <input type="text" id="subjectCode" name="subject_code" class="form-control" value="<?= esc($subject['subject_code']) ?>" required maxlength="20">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="subjectName">Subject Name</label>
                        <input type="text" id="subjectName" name="subject_name" class="form-control" value="<?= esc($subject['subject_name']) ?>" required maxlength="150">
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="units">Units</label>
                            <input type="number" id="units" name="units" class="form-control" value="<?= esc($subject['units']) ?>" min="1" max="99" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="yearLevel">Year Level</label>
                            <select id="yearLevel" name="year_level" class="form-select" required>
                                <?php foreach ([1 => '1st Year', 2 => '2nd Year', 3 => '3rd Year', 4 => '4th Year'] as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= (int) $subject['year_level'] === $value ? 'selected' : '' ?>><?= esc($label) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="semester">Semester</label>
                            <select id="semester" name="semester" class="form-select" required>
                                <option value="1st" <?= $subject['semester'] === '1st' ? 'selected' : '' ?>>1st Semester</option>
                                <option value="2nd" <?= $subject['semester'] === '2nd' ? 'selected' : '' ?>>2nd Semester</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="teacherId">Assign Faculty</label>
                        <select id="teacherId" name="teacher_id" class="form-select" required>
                            <option value="">Select faculty</option>
                            <?php foreach ($teachers as $teacher): ?>
                                <option value="<?= esc($teacher['id']) ?>" <?= (int) $subject['teacher_id'] === (int) $teacher['id'] ? 'selected' : '' ?>>
                                    <?= esc($teacher['full_name']) ?><?= !(bool) ($teacher['is_active'] ?? true) ? ' (Inactive)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-university">Save Changes</button>
                    <a href="<?= site_url('admin/subjects') ?>" class="btn btn-outline-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?= view('layouts/footer') ?>

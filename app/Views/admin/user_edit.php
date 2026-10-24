<?= view('layouts/header', get_defined_vars()) ?>

<div class="row">
    <div class="col-lg-8 col-xl-6">
        <div class="card portal-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Edit <?= esc(ucfirst($user['role'])) ?> Account</h5>
                <a href="<?= site_url($returnUrl) ?>" class="btn btn-sm btn-outline-university">Back to Directory</a>
            </div>
            <div class="card-body">
                <?php if ($error): ?><div class="alert alert-danger py-2"><?= esc($error) ?></div><?php endif; ?>

                <form method="POST" action="<?= site_url('admin/users/' . $user['id']) ?>">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label" for="fullName">Full Name</label>
                        <input type="text" id="fullName" name="full_name" class="form-control" value="<?= esc($user['full_name']) ?>" required minlength="2" maxlength="100">
                    </div>

                    <?php if ($user['role'] === 'student'): ?>
                        <div class="mb-3">
                            <label class="form-label" for="studentNumber">Student Number</label>
                            <input type="text" id="studentNumber" class="form-control" value="<?= esc($user['student_number'] ?? '') ?>" readonly>
                            <small class="text-muted">Student numbers are assigned automatically and cannot be changed.</small>
                        </div>
                    <?php else: ?>
                        <div class="mb-3">
                            <label class="form-label" for="employeeNumber">Employee ID</label>
                            <input type="text" id="employeeNumber" class="form-control" value="<?= esc($user['employee_id'] ?? '') ?>" readonly>
                            <small class="text-muted">Employee IDs are assigned automatically and cannot be changed.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="username">Username</label>
                            <input type="text" id="username" name="username" class="form-control" value="<?= esc($user['username'] ?? '') ?>" required maxlength="50">
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label" for="college">College</label>
                        <select id="college" name="college" class="form-select" required>
                            <option value="" selected>Select a college</option>
                            <option value="College of Veterinary Medicine (CVM)" <?= ($user['college'] ?? '') === 'College of Veterinary Medicine (CVM)' ? 'selected' : '' ?>>College of Veterinary Medicine (CVM)</option>
                            <option value="College of Engineering and Technology (CET)" <?= ($user['college'] ?? '') === 'College of Engineering and Technology (CET)' ? 'selected' : '' ?>>College of Engineering and Technology (CET)</option>
                            <option value="College of Education (CEd)" <?= ($user['college'] ?? '') === 'College of Education (CEd)' ? 'selected' : '' ?>>College of Education (CEd)</option>
                            <option value="College of Business and Management (CBM)" <?= ($user['college'] ?? '') === 'College of Business and Management (CBM)' ? 'selected' : '' ?>>College of Business and Management (CBM)</option>
                            <option value="College of Arts and Sciences (CAS)" <?= ($user['college'] ?? '') === 'College of Arts and Sciences (CAS)' ? 'selected' : '' ?>>College of Arts and Sciences (CAS)</option>
                            <option value="College of Agriculture and Forestry (CAF)" <?= ($user['college'] ?? '') === 'College of Agriculture and Forestry (CAF)' ? 'selected' : '' ?>>College of Agriculture and Forestry (CAF)</option>
                        </select>
                    </div>

                    <?php if ($user['role'] === 'student'): ?>
                        <div class="mb-3">
                            <label class="form-label" for="program">Program</label>
                            <select id="program" name="program" class="form-select" required data-programs="<?= esc(json_encode($programsByCollege), 'attr') ?>" data-selected-program="<?= esc($user['program'] ?? '', 'attr') ?>">
                                <option value="">Select a college first</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="yearLevel">Year Level</label>
                            <select id="yearLevel" name="year_level" class="form-select" required>
                                <option value="">Select year level</option>
                                <?php foreach (['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year', '6th Year'] as $yearLevel): ?>
                                    <option value="<?= esc($yearLevel) ?>" <?= ($user['year_level'] ?? '') === $yearLevel ? 'selected' : '' ?>><?= esc($yearLevel) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="section">Section</label>
                            <select id="section" name="section" class="form-select" required>
                                <option value="">Select section</option>
                                <?php foreach (['A', 'B', 'C'] as $section): ?>
                                    <option value="<?= $section ?>" <?= ($user['section'] ?? '') === $section ? 'selected' : '' ?>><?= $section ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label" for="password">New Password</label>
                        <input type="password" id="password" name="password" class="form-control" minlength="6" autocomplete="new-password">
                        <small class="text-muted">Leave blank to keep the current password.</small>
                    </div>

                    <button type="submit" class="btn btn-university">Save Changes</button>
                    <a href="<?= site_url($returnUrl) ?>" class="btn btn-outline-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php if ($user['role'] === 'student'): ?>
<script>
(() => {
    const collegeSelect = document.getElementById('college');
    const programSelect = document.getElementById('program');
    const programsByCollege = JSON.parse(programSelect.dataset.programs);

    const populatePrograms = (selectedProgram = '') => {
        const programs = programsByCollege[collegeSelect.value] || {};
        programSelect.replaceChildren(new Option(
            Object.keys(programs).length ? 'Select a program' : 'Select a college first',
            ''
        ));
        Object.entries(programs).forEach(([code, name]) => {
            programSelect.add(new Option(`${code} — ${name}`, code, false, code === selectedProgram));
        });
        programSelect.disabled = Object.keys(programs).length === 0;
    };

    populatePrograms(programSelect.dataset.selectedProgram);
    collegeSelect.addEventListener('change', () => populatePrograms());
})();
</script>
<?php endif; ?>

<?= view('layouts/footer') ?>

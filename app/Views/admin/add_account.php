<?= view('layouts/header', get_defined_vars()) ?>

<div class="row">
    <div class="col-lg-7 col-xl-6">
        <div class="card portal-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">New Account</h5>
            </div>
            <div class="card-body">
                <?php if ($saved): ?><div class="alert alert-success py-2"><?= esc($saved) ?></div><?php endif; ?>
                <?php if ($error): ?><div class="alert alert-danger py-2"><?= esc($error) ?></div><?php endif; ?>
                <form method="POST" action="<?= site_url('admin/add-account') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label" for="fullName">Full Name</label>
                        <input type="text" id="fullName" name="full_name" class="form-control" required minlength="2">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="roleSelect">Role</label>
                        <select name="role" class="form-select" id="roleSelect" required>
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3" id="usernameField">
                        <label class="form-label" for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control" maxlength="50">
                    </div>
                    <div class="mb-3" id="studentNumberField" hidden>
                        <label class="form-label" for="studentNumber">Student Number (assigned automatically)</label>
                        <input type="text" id="studentNumber" class="form-control" value="<?= esc($studentNumberPreview) ?>" readonly>
                        <small class="text-muted">The next number is shown as a preview and is assigned when the account is created.</small>
                    </div>
                    <div class="mb-3" id="employeeNumberField" hidden>
                        <label class="form-label" for="employeeNumber">Employee ID (assigned automatically)</label>
                        <input type="text" id="employeeNumber" class="form-control" value="<?= esc($employeeNumberPreview) ?>" readonly>
                        <small class="text-muted">The next number is shown as a preview and is assigned when the account is created.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required minlength="12" autocomplete="new-password">
                    </div>
                    <div class="mb-3" id="collegeField">
                        <label class="form-label" for="college">College</label>
                        <select id="college" name="college" class="form-select">
                            <option value="">Select a college</option>
                            <option value="College of Veterinary Medicine (CVM)">College of Veterinary Medicine (CVM)</option>
                            <option value="College of Engineering and Technology (CET)">College of Engineering and Technology (CET)</option>
                            <option value="College of Education (CEd)">College of Education (CEd)</option>
                            <option value="College of Business and Management (CBM)">College of Business and Management (CBM)</option>
                            <option value="College of Arts and Sciences (CAS)">College of Arts and Sciences (CAS)</option>
                            <option value="College of Agriculture and Forestry (CAF)">College of Agriculture and Forestry (CAF)</option>
                        </select>
                    </div>
                    <div class="mb-3" id="programField" hidden>
                        <label class="form-label" for="program">Program</label>
                        <select id="program" name="program" class="form-select" data-programs="<?= esc(json_encode($programsByCollege), 'attr') ?>">
                            <option value="">Select a college first</option>
                        </select>
                    </div>
                    <div class="mb-3" id="yearLevelField" hidden>
                        <label class="form-label" for="yearLevel">Year Level</label>
                        <select id="yearLevel" name="year_level" class="form-select">
                            <option value="">Select year level</option>
                            <?php foreach (['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year', '6th Year'] as $yearLevel): ?>
                                <option value="<?= esc($yearLevel) ?>"><?= esc($yearLevel) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3" id="sectionField" hidden>
                        <label class="form-label" for="section">Section</label>
                        <select id="section" name="section" class="form-select">
                            <option value="">Select section</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-university w-100">Create Account</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script {csp-script-nonce}>
const collegeSelect = document.getElementById('college');
const programSelect = document.getElementById('program');
const roleSelect = document.getElementById('roleSelect');
const programsByCollege = JSON.parse(programSelect.dataset.programs);

function populatePrograms(selectedProgram = '') {
    const programs = programsByCollege[collegeSelect.value] || {};
    programSelect.replaceChildren(new Option(
        Object.keys(programs).length ? 'Select a program' : 'Select a college first',
        ''
    ));
    Object.entries(programs).forEach(([code, name]) => {
        programSelect.add(new Option(`${code} — ${name}`, code, false, code === selectedProgram));
    });
    programSelect.disabled = Object.keys(programs).length === 0;
}

collegeSelect.addEventListener('change', () => populatePrograms());

function toggleRoleFields() {
    const role = roleSelect.value;
    const isStudent = role === 'student';
    document.getElementById('usernameField').hidden = isStudent;
    document.getElementById('studentNumberField').hidden = !isStudent;
    document.getElementById('employeeNumberField').hidden = isStudent;
    document.getElementById('collegeField').hidden = role === 'admin';
    document.getElementById('username').required = !isStudent;
    collegeSelect.required = role !== 'admin';
    document.getElementById('programField').hidden = !isStudent;
    document.getElementById('yearLevelField').hidden = !isStudent;
    document.getElementById('sectionField').hidden = !isStudent;
    programSelect.required = isStudent;
    document.getElementById('yearLevel').required = isStudent;
    document.getElementById('section').required = isStudent;
}
roleSelect.addEventListener('change', toggleRoleFields);
populatePrograms();
toggleRoleFields();
</script>

<?= view('layouts/footer') ?>

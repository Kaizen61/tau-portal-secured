<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <p class="text-muted">Create and review admin, teacher, and student accounts. The full student directory is on the <a href="<?= site_url('admin/students') ?>">Students</a> page.</p>

    <?php if ($saved): ?><div class="alert alert-success py-2"><?= esc($saved) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger py-2"><?= esc($error) ?></div><?php endif; ?>

    <div class="row">
        <div class="col-lg-7">
            <div class="card portal-card mb-4">
                <div class="card-header bg-white"><h5 class="mb-0">All Accounts</h5></div>
                <div class="card-body p-0">
                    <table class="table table-portal table-striped mb-0">
                        <thead><tr><th>Name</th><th>Username / Student No.</th><th>Role</th><th>College</th></tr></thead>
                        <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?= esc($u['full_name']) ?></td>
                                <td><?= esc($u['username'] ?? $u['student_number'] ?? '') ?></td>
                                <td><span class="badge badge-role-<?= esc($u['role']) ?>"><?= esc(ucfirst($u['role'])) ?></span></td>
                                <td class="small text-muted"><?= esc($u['college'] ?? '') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card portal-card">
                <div class="card-header bg-white"><h5 class="mb-0">Add New Account</h5></div>
                <div class="card-body">
                    <form method="POST" action="<?= site_url('admin/users') ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3"><label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">Role</label>
                            <select name="role" class="form-select" id="roleSelect" onchange="toggleRoleFields()">
                                <option value="student">Student</option>
                                <option value="teacher">Teacher</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="mb-3" id="usernameField">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control">
                        </div>
                        <div class="mb-3" id="studentNumberField" style="display:none;">
                            <label class="form-label">Student Number</label>
                            <input type="text" name="student_number" class="form-control" placeholder="e.g. 2024100100" maxlength="10">
                            <small class="text-muted">Format: YYYYNNNNNN. Year cannot be later than the current year.</small>
                        </div>
                        <div class="mb-3"><label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required minlength="6"></div>
                        <div class="mb-3"><label class="form-label">College / Office</label>
                            <input type="text" name="college" class="form-control"></div>
                        <div class="mb-3" id="yearLevelField" style="display:none;">
                            <label class="form-label">Year Level</label>
                            <input type="text" name="year_level" class="form-control" placeholder="e.g. 3rd Year">
                        </div>
                        <button type="submit" class="btn btn-university w-100">Create Account</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleRoleFields() {
    const isStudent = document.getElementById('roleSelect').value === 'student';
    document.getElementById('usernameField').style.display = isStudent ? 'none' : 'block';
    document.getElementById('studentNumberField').style.display = isStudent ? 'block' : 'none';
    document.getElementById('yearLevelField').style.display = isStudent ? 'block' : 'none';
}
toggleRoleFields();
</script>

<?= view('layouts/footer') ?>

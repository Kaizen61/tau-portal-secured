<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= site_url('student/dashboard') ?>">Student Dashboard</a></li>
            <li class="breadcrumb-item active">My Profile</li>
        </ol>
    </nav>

    <?php if ($saved): ?><div class="alert alert-success py-2"><?= esc($saved) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger py-2"><?= esc($error) ?></div><?php endif; ?>

    <div class="row justify-content-center g-3">
        <div class="col-md-6">
            <div class="card portal-card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Student Record</h5>
                    <a class="btn btn-sm btn-outline-university no-print" target="_blank" href="<?= site_url('student/profile/print') ?>">Print Student Record</a>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-3">
                        <tr><th class="student-number-label">Student Number</th><td><?= esc($profile['student_number'] ?? '') ?></td></tr>
                        <tr><th>College</th><td><?= esc($profile['college'] ?? '') ?></td></tr>
                        <tr><th>Program</th><td><?= esc(isset($programNames[$profile['program'] ?? '']) ? ($profile['program'] . ' — ' . $programNames[$profile['program']]) : '—') ?></td></tr>
                        <tr><th>Year Level</th><td><?= esc($profile['year_level'] ?? '') ?></td></tr>
                        <tr><th>Section</th><td><?= esc($profile['section'] ?? '—') ?></td></tr>
                    </table>
                    <form method="POST" action="<?= site_url('student/profile') ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3"><label class="form-label">Full Name</label>
                            <input type="text" name="full_name" class="form-control" value="<?= esc($profile['full_name']) ?>" required minlength="2"></div>
                        <button type="submit" class="btn btn-university btn-sm">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card portal-card">
                <div class="card-header bg-white"><h5 class="mb-0">Change Password</h5></div>
                <div class="card-body">
                    <form method="POST" action="<?= site_url('student/profile/password') ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3"><label class="form-label">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control" required minlength="8"></div>
                        <div class="mb-3"><label class="form-label">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" required minlength="8"></div>
                        <button type="submit" class="btn btn-university btn-sm">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('layouts/footer') ?>

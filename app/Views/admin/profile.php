<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <?php if ($saved): ?><div class="alert alert-success py-2"><?= esc($saved) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger py-2"><?= esc($error) ?></div><?php endif; ?>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card portal-card h-100">
                <div class="card-header bg-white"><h5 class="mb-0">Profile Details</h5></div>
                <div class="card-body d-flex flex-column">
                    <form id="adminProfileForm" method="POST" action="<?= site_url('admin/profile') ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label" for="fullName">Full Name</label>
                            <input type="text" id="fullName" name="full_name" class="form-control" value="<?= esc($profile['full_name']) ?>" minlength="2" maxlength="100" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="username">Username</label>
                            <input type="text" id="username" class="form-control" value="<?= esc($profile['username'] ?? '') ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="employeeId">Employee ID</label>
                            <input type="text" id="employeeId" class="form-control" value="<?= esc($profile['employee_id'] ?? '') ?>" readonly>
                        </div>
                    </form>
                    <button type="submit" form="adminProfileForm" class="btn btn-university mt-auto">Save Profile</button>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card portal-card h-100">
                <div class="card-header bg-white"><h5 class="mb-0">Change Password</h5></div>
                <div class="card-body">
                    <form method="POST" action="<?= site_url('admin/profile/password') ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label" for="currentPassword">Current Password</label>
                            <input type="password" id="currentPassword" name="current_password" class="form-control" required autocomplete="current-password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="newPassword">New Password</label>
                            <input type="password" id="newPassword" name="new_password" class="form-control" minlength="8" required autocomplete="new-password">
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="confirmPassword">Confirm New Password</label>
                            <input type="password" id="confirmPassword" name="confirm_password" class="form-control" minlength="8" required autocomplete="new-password">
                        </div>
                        <button type="submit" class="btn btn-university">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('layouts/footer') ?>

<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <p class="text-muted">Welcome, <?= esc(session()->get('full_name')) ?>.</p>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card portal-card stat-card h-100"><div class="card-body">
                <div class="stat-number"><?= (int)$subjectCount ?></div>
                <div class="text-muted small">Enrolled Subjects (All Time)</div>
            </div></div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card portal-card h-100"><div class="card-body">
                <h5 class="card-title">My Grades</h5>
                <p class="card-text text-muted small">View your grades and GWA per semester.</p>
                <a href="<?= site_url('student/grades') ?>" class="btn btn-university btn-sm">View Grades</a>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card portal-card h-100"><div class="card-body">
                <h5 class="card-title">My Profile</h5>
                <p class="card-text text-muted small">Review your student record details.</p>
                <a href="<?= site_url('student/profile') ?>" class="btn btn-university btn-sm">View Profile</a>
            </div></div>
        </div>
    </div>
</div>

<?= view('layouts/footer') ?>

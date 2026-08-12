<?= view('layouts/header', get_defined_vars()) ?>
<div>
    <p class="text-muted">Your attendance history per subject.</p>
    <div class="card portal-card">
        <div class="card-header bg-white"><h5 class="mb-0">WST301 — Web Systems and Technologies</h5></div>
        <div class="card-body p-0">
            <table class="table table-portal table-striped mb-0">
                <thead><tr><th>Date</th><th>Status</th></tr></thead>
                <tbody>
                    <tr><td>Jul 28, 2026</td><td><span class="badge bg-success">Present</span></td></tr>
                    <tr><td>Jul 29, 2026</td><td><span class="badge bg-success">Present</span></td></tr>
                    <tr><td>Jul 30, 2026</td><td><span class="badge bg-warning text-dark">Late</span></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= view('layouts/footer') ?>

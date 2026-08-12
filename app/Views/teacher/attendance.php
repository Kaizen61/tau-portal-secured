<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <p class="text-muted">Record and review attendance for your classes.</p>
    <div class="card portal-card">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">WST301 — Web Systems and Technologies</h5>
            <span class="badge bg-secondary">Recording coming soon</span>
        </div>
        <div class="card-body p-0">
            <table class="table table-portal table-striped mb-0">
                <thead><tr><th>Student</th><th>Jul 28</th><th>Jul 29</th><th>Jul 30</th><th>Jul 31</th></tr></thead>
                <tbody>
                    <tr><td>Juan Santos</td><td>Present</td><td>Present</td><td>Late</td><td>Present</td></tr>
                    <tr><td>Maria Reyes</td><td>Present</td><td>Absent</td><td>Present</td><td>Present</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= view('layouts/footer') ?>

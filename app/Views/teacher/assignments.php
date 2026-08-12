<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <p class="text-muted">Create assignments and review student submissions.</p>
    <div class="card portal-card mb-4">
        <div class="card-header bg-white"><h5 class="mb-0">Create Assignment</h5></div>
        <div class="card-body">
            <form>
                <div class="mb-3"><label class="form-label">Title</label><input type="text" class="form-control" disabled placeholder="Assignment title"></div>
                <div class="mb-3"><label class="form-label">Due Date</label><input type="date" class="form-control" disabled></div>
                <button type="button" class="btn btn-university" disabled>Create</button>
                <span class="badge bg-secondary ms-2">Coming soon</span>
            </form>
        </div>
    </div>
    <div class="card portal-card">
        <div class="card-header bg-white"><h5 class="mb-0">WST301 — Assignments</h5></div>
        <div class="card-body p-0">
            <table class="table table-portal table-striped mb-0">
                <thead><tr><th>Title</th><th>Due Date</th><th>Submissions</th></tr></thead>
                <tbody>
                    <tr><td>Project Proposal</td><td>Aug 2, 2026</td><td>1 / 2</td></tr>
                    <tr><td>Database Design Exercise</td><td>Jul 20, 2026</td><td>2 / 2</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= view('layouts/footer') ?>

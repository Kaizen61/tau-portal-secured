<?= view('layouts/header', get_defined_vars()) ?>
<div>
    <p class="text-muted">Assignments across your enrolled subjects.</p>
    <div class="card portal-card">
        <div class="card-body p-0">
            <table class="table table-portal table-striped mb-0">
                <thead><tr><th>Subject</th><th>Title</th><th>Due Date</th><th>Status</th></tr></thead>
                <tbody>
                    <tr><td>WST301</td><td>Project Proposal</td><td>Aug 2, 2026</td><td><span class="badge bg-warning text-dark">Pending</span></td></tr>
                    <tr><td>WST301</td><td>Database Design Exercise</td><td>Jul 20, 2026</td><td><span class="badge bg-success">Submitted</span></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= view('layouts/footer') ?>

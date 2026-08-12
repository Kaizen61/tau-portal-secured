<?= view('layouts/header', get_defined_vars()) ?>
<div>
    <p class="text-muted">Class schedule, assignment deadlines, and examination dates.</p>
    <div class="card portal-card">
        <div class="card-body p-0">
            <table class="table table-portal table-striped mb-0">
                <thead><tr><th>Date</th><th>Event</th><th>Type</th></tr></thead>
                <tbody>
                    <tr><td>Aug 2, 2026</td><td>Project Proposal Due (WST301)</td><td><span class="badge bg-warning text-dark">Deadline</span></td></tr>
                    <tr><td>Aug 12, 2026</td><td>Midterm Examinations Begin</td><td><span class="badge bg-danger">Exam</span></td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= view('layouts/footer') ?>

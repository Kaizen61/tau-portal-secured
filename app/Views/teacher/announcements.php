<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <p class="text-muted">Post announcements to students enrolled in your subjects.</p>
    <div class="card portal-card mb-4">
        <div class="card-header bg-white"><h5 class="mb-0">Post New Announcement</h5></div>
        <div class="card-body">
            <form>
                <div class="mb-3"><label class="form-label">Subject</label><select class="form-select" disabled><option>Select a subject</option></select></div>
                <div class="mb-3"><label class="form-label">Message</label><textarea class="form-control" rows="3" disabled placeholder="Write an announcement"></textarea></div>
                <button type="button" class="btn btn-university" disabled>Post</button>
                <span class="badge bg-secondary ms-2">Coming soon</span>
            </form>
        </div>
    </div>
    <div class="card portal-card">
        <div class="card-header bg-white"><h5 class="mb-0">Your Recent Posts</h5></div>
        <div class="list-group list-group-flush">
            <div class="list-group-item">
                <div class="d-flex justify-content-between"><strong>WST301 — Reminder: Project Proposal Due</strong><small class="text-muted">Jul 28, 2026</small></div>
                <p class="mb-0 small text-muted">Please submit your project proposal outline by Friday.</p>
            </div>
        </div>
    </div>
</div>

<?= view('layouts/footer') ?>

<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <p class="text-muted">Post and manage institution-wide announcements.</p>
    <div class="card portal-card mb-4">
        <div class="card-header bg-white"><h5 class="mb-0">Post New Announcement</h5></div>
        <div class="card-body">
            <form>
                <div class="mb-3"><label class="form-label">Title</label><input type="text" class="form-control" disabled placeholder="Announcement title"></div>
                <div class="mb-3"><label class="form-label">Message</label><textarea class="form-control" rows="3" disabled placeholder="Announcement details"></textarea></div>
                <button type="button" class="btn btn-university" disabled>Post Announcement</button>
                <span class="badge bg-secondary ms-2">Coming soon</span>
            </form>
        </div>
    </div>
    <div class="card portal-card">
        <div class="card-header bg-white"><h5 class="mb-0">Recent Announcements</h5></div>
        <div class="list-group list-group-flush">
            <div class="list-group-item">
                <div class="d-flex justify-content-between"><strong>Enrollment Period for Next Semester</strong><small class="text-muted">Aug 1, 2026</small></div>
                <p class="mb-0 small text-muted">Enrollment for the upcoming semester opens August 15.</p>
            </div>
            <div class="list-group-item">
                <div class="d-flex justify-content-between"><strong>Portal Maintenance Notice</strong><small class="text-muted">Jul 20, 2026</small></div>
                <p class="mb-0 small text-muted">The student portal will undergo scheduled maintenance this weekend.</p>
            </div>
        </div>
    </div>
</div>

<?= view('layouts/footer') ?>

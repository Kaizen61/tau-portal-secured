<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <p class="text-muted">Publish school-wide announcements for teachers and students.</p>

    <?php if ($saved): ?><div class="alert alert-success py-2"><?= esc($saved) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger py-2"><?= esc($error) ?></div><?php endif; ?>

    <div class="card portal-card mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><?= $announcement ? 'Edit Announcement' : 'Publish Announcement' ?></h5>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= $announcement ? site_url('admin/announcements/' . $announcement['id']) : site_url('admin/announcements') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label" for="announcementTitle">Title</label>
                    <input type="text" id="announcementTitle" name="title" class="form-control" maxlength="150" required
                           value="<?= esc($announcement['title'] ?? '') ?>" placeholder="Announcement title">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="announcementMessage">Message</label>
                    <textarea id="announcementMessage" name="message" class="form-control" rows="5" maxlength="10000" required
                              placeholder="Write the announcement"><?= esc($announcement['message'] ?? '') ?></textarea>
                </div>
                <button type="submit" class="btn btn-university"><?= $announcement ? 'Save Changes' : 'Publish Announcement' ?></button>
                <?php if ($announcement): ?>
                    <a href="<?= site_url('admin/announcements') ?>" class="btn btn-outline-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card portal-card">
        <div class="card-header bg-white"><h5 class="mb-0">Published Announcements</h5></div>
        <?php if (empty($announcements)): ?>
            <div class="card-body text-muted">There are no announcements yet.</div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($announcements as $item): ?>
                    <article class="list-group-item p-3">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                    <strong><?= esc($item['title']) ?></strong>
                                    <small class="text-muted"><?= esc(date('M j, Y g:i A', strtotime($item['created_at']))) ?></small>
                                </div>
                                <p class="mb-1 mt-2"><?= nl2br(esc($item['message'])) ?></p>
                                <small class="text-muted">Posted by <?= esc($item['created_by'] ?: 'Administrator') ?>
                                    <?php if (!empty($item['updated_at'])): ?> · Updated <?= esc(date('M j, Y g:i A', strtotime($item['updated_at']))) ?><?php endif; ?>
                                </small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="<?= site_url('admin/announcements/' . $item['id'] . '/edit') ?>" class="btn btn-sm btn-outline-university">Edit</a>
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteAnnouncementModal"
                                        data-delete-url="<?= site_url('admin/announcements/' . $item['id'] . '/delete') ?>"
                                        data-announcement-title="<?= esc($item['title'], 'attr') ?>">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="deleteAnnouncementModal" tabindex="-1" aria-labelledby="deleteAnnouncementModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="deleteAnnouncementForm" method="POST" action="">
                <?= csrf_field() ?>
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteAnnouncementModalTitle">Delete announcement?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="deleteAnnouncementModalMessage" class="mb-0"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Announcement</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(() => {
    const modal = document.getElementById('deleteAnnouncementModal');
    const form = document.getElementById('deleteAnnouncementForm');
    const message = document.getElementById('deleteAnnouncementModalMessage');

    modal.addEventListener('show.bs.modal', (event) => {
        const trigger = event.relatedTarget;
        form.action = trigger.dataset.deleteUrl;
        message.textContent = `Delete "${trigger.dataset.announcementTitle}"? It will no longer be visible to teachers and students.`;
    });
})();
</script>

<?= view('layouts/footer') ?>

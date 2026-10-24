<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <p class="text-muted">School-wide announcements from the administration.</p>
    <div class="card portal-card">
        <div class="card-header bg-white"><h5 class="mb-0">Announcements</h5></div>
        <?php if (empty($announcements)): ?>
            <div class="card-body text-muted">There are no announcements at this time.</div>
        <?php else: ?>
            <div class="list-group list-group-flush">
                <?php foreach ($announcements as $item): ?>
                    <article class="list-group-item p-3">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <strong><?= esc($item['title']) ?></strong>
                            <small class="text-muted"><?= esc(date('M j, Y g:i A', strtotime($item['created_at']))) ?></small>
                        </div>
                        <p class="mb-1 mt-2"><?= nl2br(esc($item['message'])) ?></p>
                        <small class="text-muted">Posted by <?= esc($item['created_by'] ?: 'Administrator') ?></small>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= view('layouts/footer') ?>

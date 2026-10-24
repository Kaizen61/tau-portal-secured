<?= $this->extend('layouts/print') ?>
<?= $this->section('content') ?>

<?php if (!empty($filters)): ?>
<dl class="meta-grid">
    <?php foreach ($filters as $label => $value): ?>
        <div><dt><?= esc($label) ?></dt><dd><?= esc($value) ?></dd></div>
    <?php endforeach; ?>
</dl>
<?php endif; ?>

<p class="note"><?= count($teachers) ?> teacher record<?= count($teachers) === 1 ? '' : 's' ?></p>
<table class="print-table">
    <thead>
        <tr><th>Employee ID</th><th>Full Name</th><th>Username</th><th>College</th><th>Created</th><th>Status</th></tr>
    </thead>
    <tbody>
    <?php if (empty($teachers)): ?>
        <tr><td colspan="6">No teacher records match the selected filters.</td></tr>
    <?php else: ?>
        <?php foreach ($teachers as $teacher): ?>
            <tr>
                <td><?= esc($teacher['employee_id'] ?? '—') ?></td>
                <td><?= esc($teacher['full_name']) ?></td>
                <td><?= esc($teacher['username'] ?? '—') ?></td>
                <td><?= esc($teacher['college'] ?? '—') ?></td>
                <td><?= !empty($teacher['created_at']) ? esc(date('M j, Y', strtotime($teacher['created_at']))) : '—' ?></td>
                <td><?= (int) ($teacher['is_active'] ?? 1) === 1 ? 'Active' : 'Inactive' ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>

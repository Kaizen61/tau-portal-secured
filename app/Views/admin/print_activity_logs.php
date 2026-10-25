<?= $this->extend('layouts/print') ?>
<?= $this->section('content') ?>

<?php if (!empty($filters)): ?>
<dl class="meta-grid">
    <?php foreach ($filters as $label => $value): ?>
        <div><dt><?= esc($label) ?></dt><dd><?= esc($value) ?></dd></div>
    <?php endforeach; ?>
</dl>
<?php endif; ?>

<p class="note"><?= count($logs) ?> activity log entr<?= count($logs) === 1 ? 'y' : 'ies' ?></p>
<table class="print-table activity-print-table">
    <thead>
        <tr><th>Date & Time</th><th>User</th><th>Action</th><th>Severity</th><th>Details</th><th>User-Agent</th></tr>
    </thead>
    <tbody>
    <?php if (empty($logs)): ?>
        <tr><td colspan="6">No activity logs match the selected filters.</td></tr>
    <?php else: ?>
        <?php foreach ($logs as $log): ?>
            <tr>
                <td><?= esc($log['created_at'] ?? '—') ?></td>
                <td><?= esc($log['username'] ?? '—') ?></td>
                <td><?= esc($log['action']) ?></td>
                <td><?= esc($log['severity']) ?></td>
                <td><?= esc($log['details'] ?? '—') ?></td>
                <td><?= esc($log['user_agent'] ?? '—') ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>

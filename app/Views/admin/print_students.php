<?= $this->extend('layouts/print') ?>
<?= $this->section('content') ?>

<?php if (!empty($filters)): ?>
<dl class="meta-grid">
    <?php foreach ($filters as $label => $value): ?>
        <div><dt><?= esc($label) ?></dt><dd><?= esc($value) ?></dd></div>
    <?php endforeach; ?>
</dl>
<?php endif; ?>

<p class="note"><?= count($students) ?> student record<?= count($students) === 1 ? '' : 's' ?></p>
<table class="print-table">
    <thead>
        <tr>
            <th>Student No.</th><th>Full Name</th><th>College</th><th>Program</th>
            <th>Year</th><th>Section</th><th>Created</th><th>Status</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($students)): ?>
        <tr><td colspan="8">No student records match the selected filters.</td></tr>
    <?php else: ?>
        <?php foreach ($students as $student): ?>
            <tr>
                <td><?= esc($student['student_number'] ?? '—') ?></td>
                <td><?= esc($student['full_name']) ?></td>
                <td><?= esc($student['college'] ?? '—') ?></td>
                <td><?= esc(($student['program'] ?? '—') . (isset($programNames[$student['program'] ?? '']) ? ' — ' . $programNames[$student['program']] : '')) ?></td>
                <td><?= esc($student['year_level'] ?? '—') ?></td>
                <td><?= esc($student['section'] ?? '—') ?></td>
                <td><?= !empty($student['created_at']) ? esc(date('M j, Y', strtotime($student['created_at']))) : '—' ?></td>
                <td><?= (int) ($student['is_active'] ?? 1) === 1 ? 'Active' : 'Inactive' ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>

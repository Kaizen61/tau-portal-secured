<?= $this->extend('layouts/print') ?>
<?= $this->section('content') ?>

<dl class="meta-grid">
    <div><dt>School Year</dt><dd><?= esc($schoolYear) ?></dd></div>
    <?php if ($filters['q'] !== ''): ?><div><dt>Teacher Search</dt><dd><?= esc($filters['q']) ?></dd></div><?php endif; ?>
    <?php if ($filters['college'] !== ''): ?><div><dt>College</dt><dd><?= esc($filters['college']) ?></dd></div><?php endif; ?>
    <div><dt>Semester</dt><dd><?= esc($filters['semester'] === 'all' ? 'All semesters' : $filters['semester'] . ' Semester') ?></dd></div>
</dl>

<?php if ($filters['q'] === '' && $filters['teacher_id'] === 0): ?>
<h2>Summary by Teacher</h2>
<table class="print-table">
    <thead><tr><th>Teacher</th><th>Employee ID</th><th>College</th><th>Semester</th><th>Evaluations</th><th>Total Points</th><th>Average / 5</th></tr></thead>
    <tbody>
    <?php foreach ($teachers as $teacher): ?>
        <?php foreach ($teacher['semester_summaries'] as $semester => $summary): ?>
            <tr>
                <td><?= esc($teacher['teacher_name']) ?></td>
                <td><?= esc($teacher['employee_id'] ?? '—') ?></td>
                <td><?= esc($teacher['college'] ?? '—') ?></td>
                <td><?= esc($semester) ?></td>
                <td><?= (int) $summary['evaluation_count'] ?></td>
                <td><?= (int) $summary['total_points'] ?></td>
                <td><?= $summary['average_score'] === null ? '—' : number_format($summary['average_score'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
    <?php if (empty($teachers)): ?><tr><td colspan="7">No teachers match the selected filters.</td></tr><?php endif; ?>
    </tbody>
</table>
<?php else: ?>
<?php foreach ($teachers as $teacher): ?>
    <h2><?= esc($teacher['teacher_name']) ?></h2>
    <?php if (empty($teacher['evaluations'])): ?>
        <p>No evaluations submitted for this teacher under the selected filters.</p>
    <?php else: ?>
        <table class="print-table teacher-evaluation-print-table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Student No.</th>
                    <th>Semester</th>
                    <?php foreach ($criteria as $label): ?><th><?= esc($label) ?></th><?php endforeach; ?>
                    <th>Total / Average</th>
                    <th>Submitted</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($teacher['evaluations'] as $evaluation): ?>
                <?php $total = array_sum($evaluation['scores']); ?>
                <tr>
                    <td><?= esc($evaluation['student_name']) ?></td>
                    <td><?= esc($evaluation['student_number'] ?? '—') ?></td>
                    <td><?= esc($evaluation['semester']) ?></td>
                    <?php foreach ($criteria as $key => $label): ?><td><?= esc($evaluation['scores'][$key] ?? '—') ?></td><?php endforeach; ?>
                    <td><?= count($evaluation['scores']) ?>/<?= count($criteria) ?> rated<br><?= $total ?>/<?= count($criteria) * 5 ?> points<br><?= number_format($total / count($criteria), 2) ?>/5</td>
                    <td><?= esc(date('M j, Y g:i A', strtotime($evaluation['submitted_at']))) ?></td>
                </tr>
                <?php foreach ($commentFields as $field => $label): ?>
                    <?php if (!empty($evaluation[$field])): ?>
                        <tr>
                            <td colspan="<?= count($criteria) + 5 ?>"><strong><?= esc($evaluation['student_name']) ?> — <?= esc($label) ?></strong><br><?= nl2br(esc($evaluation[$field])) ?></td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection() ?>

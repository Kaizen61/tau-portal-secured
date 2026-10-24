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
        <?php
        $semestersToPrint = in_array($filters['semester'], ['1st', '2nd'], true)
            ? [$filters['semester']]
            : ['1st', '2nd'];
        ?>
        <?php foreach ($semestersToPrint as $semester): ?>
            <?php
            $semesterEvaluations = array_values(array_filter(
                $teacher['evaluations'],
                static fn (array $evaluation): bool => $evaluation['semester'] === $semester
            ));
            if ($semesterEvaluations === []) {
                continue;
            }

            $aspectTotals = [];
            $aspectRatingCounts = [];
            $overallTotal = 0;
            $overallRatingCount = 0;
            foreach ($criteriaSections as $aspect => $aspectCriteria) {
                $aspectTotals[$aspect] = 0;
                $aspectRatingCounts[$aspect] = 0;
                foreach ($aspectCriteria as $criterion => $label) {
                    foreach ($semesterEvaluations as $evaluation) {
                        if (isset($evaluation['scores'][$criterion])) {
                            $aspectTotals[$aspect] += $evaluation['scores'][$criterion];
                            $aspectRatingCounts[$aspect]++;
                        }
                    }
                }
                $overallTotal += $aspectTotals[$aspect];
                $overallRatingCount += $aspectRatingCounts[$aspect];
            }
            ?>
            <h3><?= esc($semester) ?> Semester</h3>
            <p>Based on <?= count($semesterEvaluations) ?> student evaluation<?= count($semesterEvaluations) === 1 ? '' : 's' ?>.</p>
            <table class="print-table teacher-evaluation-summary-table">
                <thead><tr><th>Evaluation Aspect</th><th>Average Score / 5</th></tr></thead>
                <tbody>
                <?php foreach ($criteriaSections as $aspect => $aspectCriteria): ?>
                    <?php $ratingCount = $aspectRatingCounts[$aspect]; ?>
                    <tr>
                        <td><?= esc($aspect) ?></td>
                        <td><?= $ratingCount > 0 ? number_format($aspectTotals[$aspect] / $ratingCount, 2) : '—' ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th>Total Average</th>
                    <th><?= $overallRatingCount > 0 ? number_format($overallTotal / $overallRatingCount, 2) : '—' ?> / 5</th>
                </tr>
                </tbody>
            </table>
        <?php endforeach; ?>
    <?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection() ?>

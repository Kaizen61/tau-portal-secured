<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <p class="text-muted">School Year <?= esc($schoolYear) ?> · Evaluations and totals are separated by semester. Each evaluation has 22 criteria, for a maximum of 110 points; averages are calculated across ratings submitted for that teacher in that semester.</p>

    <?php if (empty($teachers)): ?>
        <div class="card portal-card"><div class="card-body text-muted">No teacher accounts found.</div></div>
    <?php else: ?>
        <div class="card portal-card mb-4">
            <div class="card-header bg-white"><h2 class="h5 mb-0">Summary by Teacher</h2></div>
            <div class="table-responsive">
                <table class="table table-portal table-striped mb-0">
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
                    </tbody>
                </table>
            </div>
        </div>

        <?php foreach ($teachers as $teacher): ?>
            <section class="card portal-card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h2 class="h5 mb-1"><?= esc($teacher['teacher_name']) ?></h2>
                        <span class="small text-muted">Semester totals and averages are listed separately in the summary above.</span>
                    </div>
                </div>
                <?php if (empty($teacher['evaluations'])): ?>
                    <div class="card-body text-muted">No evaluations submitted for this teacher.</div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-portal table-striped teacher-evaluation-report-table mb-0">
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
                                <?php $evaluationTotal = array_sum($evaluation['scores']); ?>
                                <tr>
                                    <td><?= esc($evaluation['student_name']) ?></td>
                                    <td><?= esc($evaluation['student_number'] ?? '—') ?></td>
                                    <td><?= esc($evaluation['semester']) ?></td>
                                    <?php foreach ($criteria as $key => $label): ?><td><?= esc($evaluation['scores'][$key] ?? '—') ?></td><?php endforeach; ?>
                                    <td><strong><?= count($evaluation['scores']) ?> / <?= count($criteria) ?> rated</strong><br><?= (int) $evaluationTotal ?> / <?= count($criteria) * 5 ?> points<br><?= number_format($evaluationTotal / count($criteria), 2) ?> / 5</td>
                                    <td><?= esc(date('M j, Y g:i A', strtotime($evaluation['submitted_at']))) ?></td>
                                </tr>
                                <tr class="teacher-evaluation-comment-row">
                                    <td colspan="<?= count($criteria) + 5 ?>">
                                        <details>
                                            <summary>View comments from <?= esc($evaluation['student_name']) ?></summary>
                                            <div class="teacher-evaluation-admin-comments">
                                                <?php foreach ($commentFields as $field => $label): ?>
                                                    <div>
                                                        <strong><?= esc($label) ?></strong>
                                                        <p><?= nl2br(esc($evaluation[$field] ?? 'No comment provided.')) ?></p>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </details>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?= view('layouts/footer') ?>

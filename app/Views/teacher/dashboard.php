<?php
$yearLabels = [1 => '1st Year', 2 => '2nd Year', 3 => '3rd Year', 4 => '4th Year'];
$maxEnrollment = max(1, ...array_map(static fn (array $subject): int => (int) $subject['student_count'], $subjectList));
$gradeCompletion = $totalEnrolled > 0 ? (int) round($totalGraded / $totalEnrolled * 100) : 0;
?>
<?= view('layouts/header', get_defined_vars()) ?>

<div class="teacher-dashboard">
    <section class="teacher-dashboard-welcome">
        <div>
            <span class="teacher-dashboard-kicker">Faculty overview</span>
            <h2>Welcome, <?= esc(session()->get('full_name')) ?></h2>
            <p>Review your assigned subjects, class sizes, and grade-entry progress.</p>
        </div>
        <a href="<?= site_url('teacher/profile') ?>" class="btn btn-university">View My Profile</a>
    </section>

    <div class="teacher-dashboard-toolbar">
        <div>
            <h2 class="h5 mb-1">Teaching Overview</h2>
            <p class="text-muted small mb-0">Filter the overview by year level or semester.</p>
        </div>
        <form method="GET" class="teacher-dashboard-filters">
            <select name="year_level" class="form-select form-select-sm" aria-label="Filter by year level" data-submit-on-change>
                <option value="">All Year Levels</option>
                <?php foreach ($yearLabels as $val => $label): ?>
                    <option value="<?= $val ?>" <?= $filterYear == $val ? 'selected' : '' ?>><?= esc($label) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="semester" class="form-select form-select-sm" aria-label="Filter by semester" data-submit-on-change>
                <option value="">All Semesters</option>
                <option value="1st" <?= $filterSem === '1st' ? 'selected' : '' ?>>1st Semester</option>
                <option value="2nd" <?= $filterSem === '2nd' ? 'selected' : '' ?>>2nd Semester</option>
            </select>
            <a href="<?= site_url('teacher/dashboard') ?>" class="btn btn-sm btn-university clear-filter-button">Clear</a>
        </form>
    </div>

    <section class="teacher-dashboard-stats" aria-label="Teaching statistics">
        <article class="teacher-stat-card teacher-stat-green">
            <span class="teacher-stat-label">Assigned Subjects</span>
            <strong><?= (int) $subjectCount ?></strong>
            <span class="teacher-stat-note">In the selected view</span>
        </article>
        <article class="teacher-stat-card teacher-stat-gold">
            <span class="teacher-stat-label">Total Enrollments</span>
            <strong><?= (int) $totalEnrolled ?></strong>
            <span class="teacher-stat-note">Across assigned subjects</span>
        </article>
        <article class="teacher-stat-card teacher-stat-blue">
            <span class="teacher-stat-label">Grades Completed</span>
            <strong><?= (int) $totalGraded ?></strong>
            <span class="teacher-stat-note"><?= $gradeCompletion ?>% of grade records</span>
        </article>
        <article class="teacher-stat-card teacher-stat-rose">
            <span class="teacher-stat-label">Pending Grades</span>
            <strong><?= (int) $totalPending ?></strong>
            <span class="teacher-stat-note">Records still awaiting grades</span>
        </article>
    </section>

    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <section class="card portal-card teacher-dashboard-panel h-100">
                <div class="card-header bg-white">
                    <span class="teacher-dashboard-kicker">Class sizes</span>
                    <h2 class="h5 mb-0">Enrollment by Subject</h2>
                </div>
                <div class="card-body">
                    <?php if (empty($subjectList)): ?>
                        <div class="teacher-dashboard-empty">
                            <strong>No assigned subjects in this view</strong>
                            <span>Try clearing the year or semester filters.</span>
                        </div>
                    <?php else: ?>
                        <div class="teacher-enrollment-chart" role="img" aria-label="Student enrollment counts by assigned subject">
                            <?php foreach ($subjectList as $subject): ?>
                                <?php $barWidth = (int) round((int) $subject['student_count'] / $maxEnrollment * 100); ?>
                                <div class="teacher-enrollment-row">
                                    <div class="teacher-enrollment-label">
                                        <span><?= esc($subject['subject_code']) ?> <small><?= esc($subject['subject_name']) ?></small></span>
                                        <strong><?= (int) $subject['student_count'] ?></strong>
                                    </div>
                                    <div class="teacher-enrollment-track" aria-hidden="true">
                                        <span style="width: <?= $barWidth ?>%"></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </div>
        <div class="col-lg-5">
            <section class="card portal-card teacher-dashboard-panel h-100">
                <div class="card-header bg-white">
                    <span class="teacher-dashboard-kicker">Grade entry</span>
                    <h2 class="h5 mb-0">Completion Progress</h2>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="teacher-grade-progress-summary">
                        <strong><?= $gradeCompletion ?>%</strong>
                        <span><?= (int) $totalGraded ?> of <?= (int) $totalEnrolled ?> records completed</span>
                    </div>
                    <div class="teacher-grade-progress-track" role="img" aria-label="<?= (int) $totalGraded ?> completed and <?= (int) $totalPending ?> pending grade records">
                        <span style="width: <?= $gradeCompletion ?>%"></span>
                    </div>
                    <div class="teacher-grade-legend">
                        <span><i class="teacher-grade-dot is-complete"></i>Graded <strong><?= (int) $totalGraded ?></strong></span>
                        <span><i class="teacher-grade-dot is-pending"></i>Pending <strong><?= (int) $totalPending ?></strong></span>
                    </div>
                    <p class="teacher-grade-hint mt-auto">Select a subject below to review its class list and enter grades.</p>
                </div>
            </section>
        </div>
    </div>

    <section class="card portal-card teacher-subject-panel">
        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <span class="teacher-dashboard-kicker">Your classes</span>
                <h2 class="h5 mb-0">Assigned Subjects</h2>
            </div>
            <span class="teacher-subject-count"><?= (int) $subjectCount ?> subject<?= $subjectCount === 1 ? '' : 's' ?></span>
        </div>
        <?php if (empty($subjectList)): ?>
            <div class="card-body text-muted">No assigned subjects match the selected filters.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-portal teacher-subject-table mb-0">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Year / Semester</th>
                            <th>Units</th>
                            <th>Students</th>
                            <th>Grade Progress</th>
                            <th>Pending</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($subjectList as $subject): ?>
                        <?php $subjectProgress = (int) $subject['student_count'] > 0 ? (int) round($subject['graded_count'] / $subject['student_count'] * 100) : 0; ?>
                        <tr>
                            <td>
                                <strong><?= esc($subject['subject_code']) ?></strong>
                                <span class="teacher-subject-name"><?= esc($subject['subject_name']) ?></span>
                            </td>
                            <td><?= esc($yearLabels[$subject['year_level']] ?? '—') ?><span class="teacher-table-subtext"><?= esc($subject['semester']) ?> Semester</span></td>
                            <td><?= (int) $subject['units'] ?></td>
                            <td><?= (int) $subject['student_count'] ?></td>
                            <td>
                                <div class="teacher-table-progress" aria-label="<?= $subjectProgress ?>% graded">
                                    <span style="width: <?= $subjectProgress ?>%"></span>
                                </div>
                                <small class="text-muted"><?= $subjectProgress ?>%</small>
                            </td>
                            <td><span class="teacher-pending-badge <?= $subject['pending_count'] === 0 ? 'is-clear' : '' ?>"><?= (int) $subject['pending_count'] ?></span></td>
                            <td><a href="<?= site_url('teacher/subjects/' . $subject['id']) ?>" class="btn btn-sm btn-university">View Class</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</div>

<?= view('layouts/footer') ?>

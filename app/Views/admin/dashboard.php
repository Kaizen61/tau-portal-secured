<?= view('layouts/header', get_defined_vars()) ?>

<?php
$studentTotal = array_sum(array_map(static fn (array $row): int => (int) $row['total'], $studentDistribution));
$gradeTotal = $gradedCount + $pendingCount;
$gradedPercent = $gradeTotal > 0 ? (int) round($gradedCount / $gradeTotal * 100) : 0;
?>

<div class="admin-dashboard">
    <section class="admin-dashboard-welcome mb-4">
        <div>
            <span class="admin-dashboard-kicker">Administration overview</span>
            <h2 class="h4 mb-1">Welcome, <?= esc(session()->get('full_name')) ?></h2>
            <p class="mb-0">Monitor student records, academic activity, and portal security in one place.</p>
        </div>
        <a href="<?= site_url('admin/add-account') ?>" class="btn btn-university">＋ Add Account</a>
    </section>

    <section class="admin-dashboard-stats mb-4" aria-label="Portal statistics">
        <a href="<?= site_url('admin/students') ?>" class="admin-stat-card">
            <span class="admin-stat-heading">
                <span class="admin-stat-icon admin-stat-icon-green" aria-hidden="true">♙</span>
                <span class="admin-stat-label">Active Students</span>
            </span>
            <strong id="statStudents" class="admin-stat-value"><?= (int) $studentCount ?></strong>
            <span class="admin-stat-note">View student directory <span aria-hidden="true">→</span></span>
        </a>
        <a href="<?= site_url('admin/users') ?>" class="admin-stat-card">
            <span class="admin-stat-heading">
                <span class="admin-stat-icon admin-stat-icon-gold" aria-hidden="true">♧</span>
                <span class="admin-stat-label">Active Faculty</span>
            </span>
            <strong id="statTeachers" class="admin-stat-value"><?= (int) $teacherCount ?></strong>
            <span class="admin-stat-note">View faculty directory <span aria-hidden="true">→</span></span>
        </a>
        <a href="<?= site_url('admin/subjects') ?>" class="admin-stat-card">
            <span class="admin-stat-heading">
                <span class="admin-stat-icon admin-stat-icon-blue" aria-hidden="true">▤</span>
                <span class="admin-stat-label">Subjects Offered</span>
            </span>
            <strong id="statSubjects" class="admin-stat-value"><?= (int) $subjectCount ?></strong>
            <span class="admin-stat-note">Manage subjects <span aria-hidden="true">→</span></span>
        </a>
        <a href="<?= site_url('admin/subjects') ?>" class="admin-stat-card <?= $pendingCount > 0 ? 'admin-stat-card-attention' : '' ?>">
            <span class="admin-stat-heading">
                <span class="admin-stat-icon admin-stat-icon-rose" aria-hidden="true">◷</span>
                <span class="admin-stat-label">Pending Grades</span>
            </span>
            <strong id="statPending" class="admin-stat-value"><?= (int) $pendingCount ?></strong>
            <span class="admin-stat-note">Grades awaiting entry</span>
        </a>
    </section>

    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <section class="card portal-card admin-dashboard-panel h-100">
                <div class="card-header d-flex justify-content-between align-items-center gap-2">
                    <div>
                        <span class="admin-panel-kicker">Student body</span>
                        <h2 class="h5 mb-0">Students by College</h2>
                    </div>
                    <span class="admin-panel-total"><?= $studentTotal ?> students</span>
                </div>
                <div class="card-body">
                    <?php if ($studentTotal === 0): ?>
                        <p class="text-muted mb-0">No active student records available.</p>
                    <?php else: ?>
                        <?php
                        $collegeColors = ['#1b4d3e', '#a6883c', '#527c64', '#c36f4b', '#557a95', '#876b9a', '#8b8b82'];
                        $pieStops = [];
                        $legendRows = [];
                        $piePosition = 0.0;
                        foreach ($studentDistribution as $index => $college):
                            $count = (int) $college['total'];
                            $percent = $studentTotal > 0 ? $count / $studentTotal * 100 : 0;
                            $color = $collegeColors[$index % count($collegeColors)];
                            if ($percent > 0):
                                $nextPosition = $piePosition + $percent;
                                $pieStops[] = $color . ' ' . number_format($piePosition, 2, '.', '') . '% ' . number_format($nextPosition, 2, '.', '') . '%';
                                $piePosition = $nextPosition;
                            endif;
                            $legendRows[] = [
                                'label' => $college['label'],
                                'total' => $count,
                                'percent' => $percent,
                                'color' => $color,
                            ];
                        endforeach;
                        ?>
                        <style {csp-style-nonce}>
                            .admin-college-pie-slices { background: conic-gradient(<?= esc(implode(', ', $pieStops)) ?>); }
                            <?php foreach (array_values($legendRows) as $index => $college): ?>
                            .admin-college-swatch-<?= (int) $index ?> { background-color: <?= esc($college['color']) ?>; }
                            <?php endforeach; ?>
                        </style>
                        <div class="admin-college-pie-layout">
                            <div class="admin-college-pie" role="img" aria-label="Pie chart showing student distribution across <?= count($collegeLabels) ?> colleges and unassigned or other records">
                                <span><?= (int) $studentTotal ?><small>students</small></span>
                                <i class="admin-college-pie-slices"></i>
                            </div>
                            <div class="admin-college-legend" aria-label="Student counts by college">
                                <?php foreach (array_values($legendRows) as $index => $college): ?>
                                    <div class="admin-college-legend-row">
                                        <span class="admin-college-swatch admin-college-swatch-<?= (int) $index ?>"></span>
                                        <span class="admin-college-legend-name"><?= esc($college['label']) ?></span>
                                        <strong><?= $college['total'] ?> <small>(<?= number_format($college['percent'], 1) ?>%)</small></strong>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <a href="<?= site_url('admin/students') ?>" class="admin-panel-link">View student directory <span aria-hidden="true">→</span></a>
                    <?php endif; ?>
                </div>
            </section>
        </div>

        <div class="col-lg-5">
            <section class="card portal-card admin-dashboard-panel h-100">
                <div class="card-header">
                    <span class="admin-panel-kicker">Academic workflow</span>
                    <h2 class="h5 mb-0">Grade Entry Status</h2>
                </div>
                <div class="card-body d-flex flex-column">
                    <div class="admin-grade-summary">
                        <div>
                            <strong id="gradedPercent"><?= number_format($gradedPercent) ?>%</strong>
                            <span>of grade records are complete</span>
                        </div>
                        <span id="gradeTotal" class="admin-grade-total"><?= $gradeTotal ?> total</span>
                    </div>
                        <style id="gradeProgressStyle" {csp-style-nonce}>.admin-grade-progress-done { width: <?= (int) $gradedPercent ?>%; }</style>
                        <div id="gradeProgress" class="admin-grade-progress" role="img" aria-label="<?= $gradedCount ?> graded and <?= $pendingCount ?> pending grade records">
                            <span id="gradeProgressDone" class="admin-grade-progress-done"></span>
                        </div>
                    <div class="admin-grade-legend">
                            <span><i class="admin-legend-dot admin-legend-done"></i>Graded <strong id="gradedLegend"><?= (int) $gradedCount ?></strong></span>
                        <span><i class="admin-legend-dot admin-legend-pending"></i>Pending <strong id="pendingLegend"><?= (int) $pendingCount ?></strong></span>
                    </div>
                    <div class="admin-workflow-callout mt-auto">
                        <span class="admin-workflow-icon" aria-hidden="true">✓</span>
                        <span>Track grade completion across all student enrollments.</span>
                    </div>
                    <a href="<?= site_url('admin/subjects') ?>" class="admin-panel-link mt-3">Review enrollments <span aria-hidden="true">→</span></a>
                </div>
            </section>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-7">
            <section class="card portal-card admin-dashboard-panel h-100">
                <div class="card-header d-flex justify-content-between align-items-center gap-2">
                    <div>
                        <span class="admin-panel-kicker">School-wide updates</span>
                        <h2 class="h5 mb-0">Recent Announcements</h2>
                    </div>
                    <a href="<?= site_url('admin/announcements') ?>" class="btn btn-sm btn-outline-university">Manage</a>
                </div>
                <?php if (empty($recentAnnouncements)): ?>
                    <div class="card-body text-muted">No announcements have been published yet.</div>
                <?php else: ?>
                    <div class="admin-dashboard-list">
                        <?php foreach ($recentAnnouncements as $announcement): ?>
                            <article class="admin-announcement-item">
                                <span class="admin-announcement-mark" aria-hidden="true"></span>
                                <div>
                                    <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                                        <h3 class="h6 mb-1"><?= esc($announcement['title']) ?></h3>
                                        <time class="small text-muted" datetime="<?= esc(date('c', strtotime($announcement['created_at'])), 'attr') ?>"><?= esc(date('M j, Y', strtotime($announcement['created_at']))) ?></time>
                                    </div>
                                    <p class="small text-muted mb-0"><?= esc(mb_strimwidth($announcement['message'], 0, 150, '…')) ?></p>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </section>
        </div>

        <div class="col-lg-5">
            <section class="card portal-card admin-dashboard-panel h-100">
                <div class="card-header d-flex justify-content-between align-items-center gap-2">
                    <div>
                        <span class="admin-panel-kicker">Portal monitoring</span>
                        <h2 class="h5 mb-0">Security & Activity</h2>
                    </div>
                    <a href="<?= site_url('admin/audit-trail') ?>" class="btn btn-sm btn-outline-university">System Activity Logs</a>
                </div>
                <div class="card-body">
                    <div id="heroTile" class="admin-security-status <?= $highSeverityCount > 0 ? 'has-alerts' : '' ?>">
                        <span class="admin-security-icon" aria-hidden="true"><?= $highSeverityCount > 0 ? '!' : '✓' ?></span>
                        <div>
                            <strong id="heroLabel"><?= $highSeverityCount > 0 ? 'Security alert' : 'All clear' ?></strong>
                            <p id="heroDesc" class="mb-1"><?= $highSeverityCount > 0 ? 'High-severity events in the last 24 hours.' : 'No high-severity events in the last 24 hours.' ?></p>
                            <span><strong id="heroNumber"><?= (int) $highSeverityCount ?></strong> recent alerts</span>
                        </div>
                    </div>
                    <?php if (!empty($recentActivity)): ?>
                        <h3 class="admin-activity-heading">Latest activity</h3>
                        <div class="admin-activity-list">
                            <?php foreach ($recentActivity as $activity): ?>
                                <?php $isHighSeverity = $activity['severity'] === 'HIGH'; ?>
                                <div class="admin-activity-item">
                                    <span class="admin-activity-indicator <?= $isHighSeverity ? 'is-high' : '' ?>" aria-hidden="true"></span>
                                    <div class="flex-grow-1">
                                        <strong><?= esc(str_replace('_', ' ', $activity['action'])) ?></strong>
                                        <span><?= esc($activity['username'] ?: 'System') ?></span>
                                    </div>
                                    <time><?= esc(date('M j, g:i A', strtotime($activity['created_at']))) ?></time>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted small mt-3 mb-0">No recent activity to display.</p>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </div>

</div>

<script {csp-script-nonce}>
async function pollDashboard() {
    try {
        const response = await fetch('<?= site_url('admin/dashboard/poll') ?>', {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        if (!response.ok) {
            return;
        }

        const data = await response.json();
        document.getElementById('statStudents').textContent = data.studentCount;
        document.getElementById('statTeachers').textContent = data.teacherCount;
        document.getElementById('statSubjects').textContent = data.subjectCount;
        document.getElementById('statPending').textContent = data.pendingCount;
        document.getElementById('pendingLegend').textContent = data.pendingCount;
        document.getElementById('gradedLegend').textContent = data.gradedCount;
        const gradeTotal = data.gradedCount + data.pendingCount;
        const gradedPercent = gradeTotal > 0 ? Math.round(data.gradedCount / gradeTotal * 100) : 0;
        document.getElementById('gradeTotal').textContent = `${gradeTotal} total`;
        document.getElementById('gradedPercent').textContent = `${gradedPercent}%`;
        document.getElementById('gradeProgressStyle').sheet.cssRules[0].style.width = `${gradedPercent}%`;
        document.getElementById('gradeProgress').setAttribute(
            'aria-label',
            `${data.gradedCount} graded and ${data.pendingCount} pending grade records`
        );

        const hero = document.getElementById('heroTile');
        const label = document.getElementById('heroLabel');
        const number = document.getElementById('heroNumber');
        const description = document.getElementById('heroDesc');
        number.textContent = data.highSeverityCount;
        hero.classList.toggle('has-alerts', data.highSeverityCount > 0);
        label.textContent = data.highSeverityCount > 0 ? 'Security alert' : 'All clear';
        description.textContent = data.highSeverityCount > 0
            ? 'High-severity events in the last 24 hours.'
            : 'No high-severity events in the last 24 hours.';
        hero.querySelector('.admin-security-icon').textContent = data.highSeverityCount > 0 ? '!' : '✓';
    } catch (error) {
        console.error('Could not refresh the admin dashboard.', error);
    }
}

setInterval(pollDashboard, 5000);
</script>

<?= view('layouts/footer') ?>

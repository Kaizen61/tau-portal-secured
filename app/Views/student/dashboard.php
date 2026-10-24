<?= view('layouts/header', get_defined_vars()) ?>

<?php
$metrics = [
    ['label' => 'Current GWA', 'value' => $currentGwa !== null ? number_format($currentGwa, 2) : '—', 'note' => 'School Year ' . $schoolYear, 'icon' => '◎'],
    ['label' => 'Pending Grades', 'value' => (string) $pendingGrades, 'note' => 'Subjects awaiting a grade', 'icon' => '◷'],
    ['label' => 'Units Enrolled', 'value' => (string) $unitsEnrolled, 'note' => 'Units this school year', 'icon' => '▤'],
    ['label' => 'Enrolled Subjects', 'value' => (string) $enrolledSubjects, 'note' => 'Subjects this school year', 'icon' => '▦'],
];
?>

<div class="student-dashboard">
    <div class="dashboard-welcome mb-4">
        <div>
            <p class="dashboard-kicker mb-1">Your academic overview</p>
            <h2 class="h4 mb-1">Welcome, <?= esc(session()->get('full_name')) ?></h2>
            <p class="text-muted mb-0">A quick look at your progress for School Year <?= esc($schoolYear) ?>.</p>
        </div>
        <a href="<?= site_url('student/profile') ?>" class="btn btn-outline-university">View Student Profile</a>
    </div>

    <div class="row g-3 mb-4">
        <?php foreach ($metrics as $metric): ?>
            <div class="col-6 col-xl-3">
                <section class="card portal-card dashboard-metric h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start gap-2">
                            <span class="dashboard-metric-icon" aria-hidden="true"><?= esc($metric['icon']) ?></span>
                            <?php if ($metric['label'] === 'Current GWA' && $currentGwa !== null): ?>
                                <span class="dashboard-pill">Current</span>
                            <?php endif; ?>
                        </div>
                        <div class="dashboard-metric-value"><?= esc($metric['value']) ?></div>
                        <h3 class="dashboard-metric-label"><?= esc($metric['label']) ?></h3>
                        <p class="dashboard-metric-note mb-0"><?= esc($metric['note']) ?></p>
                    </div>
                </section>
            </div>
        <?php endforeach; ?>
    </div>

    <section class="card portal-card dashboard-announcements mb-4">
        <div class="card-header d-flex justify-content-between align-items-center gap-2">
            <div>
                <span class="dashboard-section-kicker">Stay informed</span>
                <h2 class="h5 mb-0">Recent Announcements</h2>
            </div>
            <a href="<?= site_url('student/announcements') ?>" class="btn btn-sm btn-outline-university">All announcements</a>
        </div>
        <?php if (empty($announcements)): ?>
            <div class="card-body dashboard-empty">
                <span class="dashboard-empty-icon" aria-hidden="true">✦</span>
                <div>
                    <strong>You’re all caught up</strong>
                    <p class="text-muted mb-0">There are no announcements to show right now.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="dashboard-announcement-list">
                <?php foreach ($announcements as $item): ?>
                    <article class="dashboard-announcement">
                        <span class="dashboard-announcement-dot" aria-hidden="true"></span>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start gap-2 flex-wrap">
                                <h3 class="h6 mb-1"><?= esc($item['title']) ?></h3>
                                <time class="small text-muted" datetime="<?= esc(date('c', strtotime($item['created_at'])), 'attr') ?>">
                                    <?= esc(date('M j, Y', strtotime($item['created_at']))) ?>
                                </time>
                            </div>
                            <p class="dashboard-announcement-message mb-0"><?= esc(mb_strimwidth($item['message'], 0, 230, '…')) ?></p>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <div class="dashboard-quick-links mb-4">
        <a class="card portal-card dashboard-link-card" href="<?= site_url('student/grades') ?>">
            <span class="dashboard-link-icon" aria-hidden="true">↗</span>
            <span><strong>My Grades</strong><small>Review grades and semester results</small></span>
            <span class="dashboard-link-arrow" aria-hidden="true">→</span>
        </a>
        <a class="card portal-card dashboard-link-card" href="<?= site_url('student/courses') ?>">
            <span class="dashboard-link-icon" aria-hidden="true">▤</span>
            <span><strong>My Courses</strong><small>See subjects and enrolled units</small></span>
            <span class="dashboard-link-arrow" aria-hidden="true">→</span>
        </a>
        <a class="card portal-card dashboard-link-card" href="<?= site_url('student/profile') ?>">
            <span class="dashboard-link-icon" aria-hidden="true">◎</span>
            <span><strong>Profile</strong><small>View your student record</small></span>
            <span class="dashboard-link-arrow" aria-hidden="true">→</span>
        </a>
    </div>

    <section class="card portal-card dashboard-gwa-card mb-4">
        <div class="card-header">
            <span class="dashboard-section-kicker">Academic performance</span>
            <h2 class="h5 mb-0">Grade Performance</h2>
        </div>
        <div class="card-body">
            <div class="row align-items-stretch g-4">
                <div class="col-lg-3">
                    <section class="dashboard-overall-card h-100" aria-label="Overall GWA">
                        <span class="dashboard-overall-card-label">Overall GWA</span>
                        <p class="dashboard-overall-value mb-2"><?= $overallGwa !== null ? number_format($overallGwa, 2) : '—' ?></p>
                        <p class="dashboard-overall-card-description mb-0">Units-weighted average across all graded subjects.</p>
                    <?php if ($overallGwa === null): ?>
                            <span class="dashboard-empty-hint">Your GWA will appear after grades are posted.</span>
                    <?php endif; ?>
                    </section>
                </div>
                <div class="col-lg-9">
                    <h3 class="dashboard-chart-title">GWA by school year and semester</h3>
                    <?php if (empty($gwaTrend)): ?>
                        <p class="text-muted small mb-0">No graded semesters to chart yet.</p>
                    <?php else: ?>
                        <?php
                        $termCount = count($gwaTrend);
                        $chartWidth = max(660, 180 + ($termCount * 150));
                        $plotLeft = 100;
                        $plotRight = $chartWidth - 100;
                        $plotTop = 30;
                        $plotBottom = 178;
                        $plotWidth = $plotRight - $plotLeft;
                        $plotHeight = $plotBottom - $plotTop;
                        $chartPoints = [];
                        foreach ($gwaTrend as $index => $term) {
                            $gwa = max(1, min(5, (float) $term['gwa']));
                            $x = $termCount === 1
                                ? $plotLeft + $plotWidth / 2
                                : $plotLeft + ($index * $plotWidth / ($termCount - 1));
                            $chartPoints[] = [
                                'x' => $x,
                                'y' => $plotTop + (($gwa - 1) / 4 * $plotHeight),
                                'gwa' => $gwa,
                                'label' => $term['label'],
                            ];
                        }
                        $linePoints = implode(' ', array_map(
                            static fn (array $point): string => number_format($point['x'], 2, '.', '') . ',' . number_format($point['y'], 2, '.', ''),
                            $chartPoints
                        ));
                        $areaPoints = number_format($chartPoints[0]['x'], 2, '.', '') . ',' . $plotBottom
                            . ' ' . $linePoints
                            . ' ' . number_format($chartPoints[count($chartPoints) - 1]['x'], 2, '.', '') . ',' . $plotBottom;
                        ?>
                        <div class="dashboard-dotplot" data-dotplot-viewport>
                            <svg class="dashboard-dotplot-canvas" width="<?= $chartWidth ?>" height="246" viewBox="0 0 <?= $chartWidth ?> 246" role="img" aria-label="Draggable GWA by school year line chart with shaded area, on a 1.00 to 5.00 college grading scale">
                                <defs>
                                    <linearGradient id="gwaAreaGradient" x1="0" y1="0" x2="0" y2="1">
                                        <stop offset="0%" stop-color="#1b4d3e" stop-opacity="0.28"></stop>
                                        <stop offset="100%" stop-color="#1b4d3e" stop-opacity="0.03"></stop>
                                    </linearGradient>
                                </defs>
                                <?php foreach ([1, 2, 3, 4, 5] as $tick): ?>
                                    <?php $tickY = $plotTop + (($tick - 1) / 4 * $plotHeight); ?>
                                    <line x1="<?= $plotLeft ?>" y1="<?= $tickY ?>" x2="<?= $plotRight ?>" y2="<?= $tickY ?>" class="dashboard-dotplot-grid"></line>
                                    <text x="<?= $plotLeft - 10 ?>" y="<?= $tickY + 4 ?>" text-anchor="end" class="dashboard-dotplot-axis"><?= number_format((float) $tick, 2) ?></text>
                                <?php endforeach; ?>
                                <polygon points="<?= esc($areaPoints, 'attr') ?>" class="dashboard-dotplot-area"></polygon>
                                <line x1="<?= $plotLeft ?>" y1="<?= $plotTop ?>" x2="<?= $plotLeft ?>" y2="<?= $plotBottom ?>" class="dashboard-dotplot-axis-line"></line>
                                <line x1="<?= $plotLeft ?>" y1="<?= $plotBottom ?>" x2="<?= $plotRight ?>" y2="<?= $plotBottom ?>" class="dashboard-dotplot-axis-line"></line>
                                <polyline points="<?= esc($linePoints, 'attr') ?>" class="dashboard-dotplot-line"></polyline>
                                <?php foreach ($chartPoints as $index => $point): ?>
                                    <?php
                                    $x = $point['x'];
                                    $y = $point['y'];
                                    $gwa = $point['gwa'];
                                    [$yearLabel, $termLabel] = explode(' · ', $point['label'], 2);
                                    $valueIsLast = $index === $termCount - 1 && $termCount > 1;
                                    $valueX = $x + ($valueIsLast ? -11 : 11);
                                    ?>
                                    <circle cx="<?= number_format($x, 2, '.', '') ?>" cy="<?= number_format($y, 2, '.', '') ?>" r="7" class="dashboard-dotplot-point">
                                        <title><?= esc($point['label']) ?>: <?= number_format($gwa, 2) ?></title>
                                    </circle>
                                    <text x="<?= number_format($valueX, 2, '.', '') ?>" y="<?= number_format($y - 9, 2, '.', '') ?>" text-anchor="<?= $valueIsLast ? 'end' : 'start' ?>" class="dashboard-dotplot-value"><?= number_format($gwa, 2) ?></text>
                                    <text x="<?= number_format($x, 2, '.', '') ?>" y="<?= $plotBottom + 20 ?>" text-anchor="middle" class="dashboard-dotplot-term">
                                        <tspan x="<?= number_format($x, 2, '.', '') ?>"><?= esc($yearLabel) ?></tspan>
                                        <tspan x="<?= number_format($x, 2, '.', '') ?>" dy="14"><?= esc($termLabel) ?></tspan>
                                    </text>
                                <?php endforeach; ?>
                                <text x="18" y="104" text-anchor="middle" class="dashboard-dotplot-title" transform="rotate(-90 18 104)">GWA</text>
                            </svg>
                        </div>
                        <p class="dashboard-chart-caption mb-0">Drag the chart left or right to view every school year and semester. Hover over a dot to see its exact GWA.</p>
                        <script {csp-script-nonce}>
                        (() => {
                            const viewport = document.querySelector('[data-dotplot-viewport]');
                            const chart = viewport.querySelector('.dashboard-dotplot-canvas');
                            let pointerStart = null;
                            let offset = 0;

                            const limitOffset = (value) => {
                                const maxOffset = Math.min(0, viewport.clientWidth - chart.getBoundingClientRect().width);
                                return Math.max(maxOffset, Math.min(0, value));
                            };

                            const updatePosition = () => {
                                offset = limitOffset(offset);
                                chart.style.transform = `translateX(${offset}px)`;
                                viewport.classList.toggle('is-draggable', viewport.clientWidth < chart.getBoundingClientRect().width);
                            };

                            viewport.addEventListener('pointerdown', (event) => {
                                if ((event.pointerType !== 'touch' && event.button !== 0) || viewport.clientWidth >= chart.getBoundingClientRect().width) {
                                    return;
                                }

                                pointerStart = { x: event.clientX, offset };
                                viewport.classList.add('is-dragging');
                                viewport.setPointerCapture(event.pointerId);
                            });

                            viewport.addEventListener('pointermove', (event) => {
                                if (pointerStart === null) {
                                    return;
                                }

                                offset = limitOffset(pointerStart.offset + event.clientX - pointerStart.x);
                                chart.style.transform = `translateX(${offset}px)`;
                            });

                            const endDrag = () => {
                                pointerStart = null;
                                viewport.classList.remove('is-dragging');
                            };
                            viewport.addEventListener('pointerup', endDrag);
                            viewport.addEventListener('pointercancel', endDrag);
                            window.addEventListener('resize', updatePosition);
                            updatePosition();
                        })();
                        </script>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<?= view('layouts/footer') ?>

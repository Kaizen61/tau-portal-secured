<?php $yearLabels = [1 => '1st Year', 2 => '2nd Year', 3 => '3rd Year', 4 => '4th Year']; ?>
<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb small">
            <li class="breadcrumb-item"><a href="<?= site_url('student/dashboard') ?>">Student Dashboard</a></li>
            <li class="breadcrumb-item active">My Grades</li>
        </ol>
    </nav>

    <form method="GET" class="d-flex gap-2 justify-content-end mb-3">
        <select name="school_year" class="form-select form-select-sm grade-filter-select" data-submit-on-change>
            <option value="">All School Years</option>
            <?php foreach ($schoolYears as $sy): ?>
                <option value="<?= esc($sy['school_year']) ?>" <?= $filterSy === $sy['school_year'] ? 'selected' : '' ?>><?= esc($sy['school_year']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="semester" class="form-select form-select-sm grade-filter-select" data-submit-on-change>
            <option value="">All Semesters</option>
            <option value="1st" <?= $filterSem === '1st' ? 'selected' : '' ?>>1st Semester</option>
            <option value="2nd" <?= $filterSem === '2nd' ? 'selected' : '' ?>>2nd Semester</option>
        </select>
        <a href="<?= site_url('student/grades') ?>" class="btn btn-sm btn-outline-university clear-filter-button">Clear</a>
    </form>

    <?php if (empty($groups)): ?>
        <div class="card portal-card"><div class="card-body text-muted">No grade records found for this filter.</div></div>
    <?php endif; ?>

    <?php foreach ($groups as $group): ?>
    <div class="card portal-card mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-0"><?= esc($yearLabels[$group['year_level']] ?? '') ?> &middot; <?= esc($group['semester']) ?> Semester</h5>
                <span class="text-muted small">School Year <?= esc($group['school_year']) ?></span>
            </div>
            <div class="text-end">
                <div class="eyebrow mb-0">General Weighted Average</div>
                <div class="stat-number grade-stat-number">
                    <?= $group['gwa'] !== null ? esc($group['gwa']) : '—' ?>
                </div>
                <a class="btn btn-sm btn-outline-university mt-2 no-print" target="_blank"
                   href="<?= site_url('student/grades/print') ?>?school_year=<?= urlencode($group['school_year']) ?>&semester=<?= urlencode($group['semester']) ?>">
                    Print Grade Report
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-portal table-striped mb-0">
                <thead><tr><th>Code</th><th>Subject</th><th>Units</th><th>Grade</th></tr></thead>
                <tbody>
                <?php foreach ($group['subjects'] as $s): ?>
                    <tr>
                        <td><?= esc($s['subject_code']) ?></td>
                        <td><?= esc($s['subject_name']) ?></td>
                        <td><?= esc($s['units']) ?></td>
                        <td>
                            <?php if ($s['grade'] === null): ?>
                                <span class="badge bg-secondary">Not yet graded</span>
                            <?php else: ?><?= esc($s['grade']) ?><?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white small text-muted">
            GWA = units-weighted average of graded subjects in this semester only. Ungraded subjects are excluded, not counted as zero.
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?= view('layouts/footer') ?>

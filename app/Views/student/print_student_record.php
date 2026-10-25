<?= $this->extend('layouts/print') ?>
<?= $this->section('content') ?>

<dl class="meta-grid">
    <div>
        <dt>Full Name</dt>
        <dd><?= esc($profile['full_name'] ?? '') ?></dd>
        <dt>Student Number</dt>
        <dd><?= esc($profile['student_number'] ?? '—') ?></dd>
        <dt>College / Program</dt>
        <dd><?= esc(($profile['college'] ?? '—') . (!empty($profile['program']) && isset($programNames[$profile['program']]) ? ' / ' . $profile['program'] . ' — ' . $programNames[$profile['program']] : '')) ?></dd>
        <dt>Section</dt>
        <dd><?= esc($profile['section'] ?? '—') ?></dd>
    </div>
    <div>
        <dt>Year Level</dt>
        <dd><?= esc($profile['year_level'] ?? '—') ?></dd>
        <dt>Record Status</dt>
        <dd>Enrolled</dd>
        <dt>Date Generated</dt>
        <dd><?= esc($printedAt) ?></dd>
    </div>
</dl>

<h3 class="enrollment-history-heading">Enrollment History</h3>
<table class="print-table">
    <thead>
        <tr>
            <th>School Year</th>
            <th>Sem</th>
            <th>Code</th>
            <th>Subject</th>
            <th>Units</th>
            <th>Grade</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($records)): ?>
        <tr><td colspan="6">No enrollment records on file.</td></tr>
    <?php else: ?>
        <?php foreach ($records as $row): ?>
        <tr>
            <td><?= esc($row['school_year']) ?></td>
            <td><?= esc($row['semester']) ?></td>
            <td><?= esc($row['subject_code']) ?></td>
            <td><?= esc($row['subject_name']) ?></td>
            <td><?= esc($row['units']) ?></td>
            <td><?= $row['grade'] === null ? 'NG' : esc($row['grade']) ?></td>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<p class="note">This student record is generated from the TAU portal for the student’s own copy. Official transcripts must still be requested from the Registrar.</p>

<div class="sign-row">
    <div class="sign-block"><div class="sign-line">Student</div></div>
    <div class="sign-block"><div class="sign-line">College Dean</div></div>
    <div class="sign-block"><div class="sign-line">Registrar</div></div>
</div>

<?= $this->endSection() ?>

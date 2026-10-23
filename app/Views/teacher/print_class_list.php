<?= $this->extend('layouts/print') ?>
<?= $this->section('content') ?>

<dl class="meta-grid">
    <div>
        <dt>Subject Code</dt>
        <dd><?= esc($subject['subject_code']) ?></dd>
        <dt>Descriptive Title</dt>
        <dd><?= esc($subject['subject_name']) ?></dd>
        <dt>Units</dt>
        <dd><?= esc($subject['units']) ?></dd>
    </div>
    <div>
        <dt>Faculty</dt>
        <dd><?= esc($facultyName) ?></dd>
        <dt>Semester</dt>
        <dd><?= esc($subject['semester']) ?> Semester</dd>
        <dt>Year Level</dt>
        <dd><?php $yl = [1 => '1st Year', 2 => '2nd Year', 3 => '3rd Year', 4 => '4th Year']; echo esc($yl[$subject['year_level']] ?? $subject['year_level']); ?></dd>
    </div>
</dl>

<table class="print-table">
    <thead>
        <tr>
            <th style="width:40px;">#</th>
            <th>Student No.</th>
            <th>Student Name</th>
            <th>Year Level</th>
            <th>School Year</th>
            <th>Final Grade</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($students)): ?>
        <tr><td colspan="6">No students enrolled in this subject.</td></tr>
    <?php else: ?>
        <?php foreach ($students as $i => $row): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= esc($row['student_number'] ?? '—') ?></td>
            <td><?= esc($row['full_name']) ?></td>
            <td><?= esc($row['year_level'] ?? '') ?></td>
            <td><?= esc($row['school_year']) ?></td>
            <td><?= $row['grade'] === null ? '' : esc($row['grade']) ?></td>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<p class="note">Class list / grade sheet for submission to the Registrar. Leave the grade blank if the student is not yet graded. Total enrolled: <?= count($students) ?>.</p>

<div class="sign-row">
    <div class="sign-block"><div class="sign-line">Faculty-in-Charge</div></div>
    <div class="sign-block"><div class="sign-line">Department Chair</div></div>
    <div class="sign-block"><div class="sign-line">Registrar</div></div>
</div>

<?= $this->endSection() ?>

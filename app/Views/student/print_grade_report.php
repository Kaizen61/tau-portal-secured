<?php
$yearLabels = [1 => '1st Year', 2 => '2nd Year', 3 => '3rd Year', 4 => '4th Year'];
?>
<?= $this->extend('layouts/print') ?>
<?= $this->section('content') ?>

<dl class="meta-grid">
    <div>
        <dt>Student Name</dt>
        <dd><?= esc($profile['full_name'] ?? '') ?></dd>
        <dt>Student Number</dt>
        <dd><?= esc($profile['student_number'] ?? '—') ?></dd>
        <dt>College</dt>
        <dd><?= esc($profile['college'] ?? '—') ?></dd>
        <dt>Section</dt>
        <dd><?= esc($profile['section'] ?? '—') ?></dd>
    </div>
    <div>
        <dt>Year Level</dt>
        <dd><?= esc($yearLabels[$group['year_level']] ?? ($profile['year_level'] ?? '—')) ?></dd>
        <dt>School Year</dt>
        <dd><?= esc($group['school_year']) ?></dd>
        <dt>Semester</dt>
        <dd><?= esc($group['semester']) ?> Semester</dd>
    </div>
</dl>

<table class="print-table">
    <thead>
        <tr>
            <th>Code</th>
            <th>Descriptive Title</th>
            <th>Units</th>
            <th>Final Grade</th>
        </tr>
    </thead>
    <tbody>
    <?php $totalUnits = 0; foreach ($group['subjects'] as $s): $totalUnits += (int) $s['units']; ?>
        <tr>
            <td><?= esc($s['subject_code']) ?></td>
            <td><?= esc($s['subject_name']) ?></td>
            <td><?= esc($s['units']) ?></td>
            <td><?= $s['grade'] === null ? 'INC / NG' : esc($s['grade']) ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">Total units enrolled</td>
            <td><?= $totalUnits ?></td>
            <td></td>
        </tr>
    </tfoot>
</table>

<div class="gwa-box">
    <div>
        <div class="label">General Weighted Average</div>
        <div class="note grade-report-note">Units-weighted average of graded subjects only. Ungraded subjects are excluded.</div>
    </div>
    <div class="value"><?= $group['gwa'] !== null ? esc($group['gwa']) : '—' ?></div>
</div>

<div class="sign-row">
    <div class="sign-block"><div class="sign-line">Student</div></div>
    <div class="sign-block"><div class="sign-line">Program Chair</div></div>
    <div class="sign-block"><div class="sign-line">Registrar</div></div>
</div>

<?= $this->endSection() ?>

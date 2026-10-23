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
            <th>Subject</th>
            <th>Units</th>
            <th>Instructor</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($group['subjects'] as $s): ?>
        <tr>
            <td><?= esc($s['subject_code']) ?></td>
            <td><?= esc($s['subject_name']) ?></td>
            <td><?= esc($s['units']) ?></td>
            <td><?= esc($s['teacher_name'] ?? 'Unassigned') ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="2">Total units</td>
            <td><?= (int) $group['total_units'] ?></td>
            <td></td>
        </tr>
    </tfoot>
</table>

<p class="note">This Certificate of Registration lists subjects in which the student is enrolled for the indicated term. It is not a report of grades.</p>

<div class="sign-row">
    <div class="sign-block"><div class="sign-line">Student</div></div>
    <div class="sign-block"><div class="sign-line">Adviser</div></div>
    <div class="sign-block"><div class="sign-line">Registrar</div></div>
</div>

<?= $this->endSection() ?>

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
        <dt><?= count($schoolYears) === 1 ? 'School Year' : 'School Years' ?></dt>
        <dd><?= esc($schoolYears ? implode(', ', $schoolYears) : '—') ?></dd>
        <dt>Semester</dt>
        <dd><?= esc($subject['semester']) ?> Semester</dd>
        <dt>Year Level</dt>
        <dd><?php $yl = [1 => '1st Year', 2 => '2nd Year', 3 => '3rd Year', 4 => '4th Year']; echo esc($yl[$subject['year_level']] ?? $subject['year_level']); ?></dd>
    </div>
</dl>

<table class="print-table class-list-print-table <?= $includeGrades ? 'has-grades' : 'without-grades' ?>">
    <?php if ($includeGrades): ?>
        <colgroup>
            <col>
            <col>
            <col>
            <col>
            <col>
            <col>
        </colgroup>
    <?php else: ?>
        <colgroup>
            <col>
            <col>
            <col>
            <col>
            <col>
        </colgroup>
    <?php endif; ?>
    <thead>
        <tr>
            <th>#</th>
            <th>Student No.</th>
            <th>Student Name</th>
            <th>Year Level</th>
            <th>Section</th>
            <?php if ($includeGrades): ?><th>Final Grade</th><?php endif; ?>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($students)): ?>
        <tr><td colspan="<?= $includeGrades ? 6 : 5 ?>">No students enrolled in this subject.</td></tr>
    <?php else: ?>
        <?php foreach ($students as $i => $row): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= view('components/student_number', ['studentNumber' => $row['student_number'] ?? '—']) ?></td>
            <td><?= esc($row['full_name']) ?></td>
            <td><?= esc($row['year_level'] ?? '') ?></td>
            <td><?= esc($row['section'] ?? '—') ?></td>
            <?php if ($includeGrades): ?><td><?= $row['grade'] === null ? '' : esc($row['grade']) ?></td><?php endif; ?>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<div class="sign-row">
    <div class="sign-block"><div class="sign-line">Faculty-in-Charge</div></div>
    <div class="sign-block"><div class="sign-line">Department Chair</div></div>
    <div class="sign-block"><div class="sign-line">Registrar</div></div>
</div>

<?= $this->endSection() ?>

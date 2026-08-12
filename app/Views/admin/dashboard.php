<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <p class="text-muted">Welcome, <?= esc(session()->get('full_name')) ?>.</p>

    <div class="bento-grid">

        <!-- Hero tile: high-severity alert or a calm welcome state -->
        <div class="bento-tile bento-tile-large <?= $highSeverityCount > 0 ? 'bento-tile-alert' : 'bento-tile-dark' ?>" id="heroTile">
            <div>
                <div class="bento-label" id="heroLabel"><?= $highSeverityCount > 0 ? 'Security Alert' : 'All Clear' ?></div>
                <div class="bento-number" id="heroNumber"><?= (int)$highSeverityCount ?></div>
            </div>
            <div>
                <p class="mb-2" id="heroDesc" style="max-width:320px;">
                    <?= $highSeverityCount > 0
                        ? 'high-severity security alert(s) in the last 24 hours (brute-force lockouts, honeytoken triggers).'
                        : 'No high-severity security alerts in the last 24 hours.' ?>
                </p>
                <a href="<?= site_url('admin/audit-trail') ?>?severity=HIGH" class="btn btn-sm btn-outline-light">Review Audit Trail</a>
            </div>
        </div>

        <div class="bento-tile bento-tile-tall">
            <div class="bento-label">Students</div>
            <div class="bento-number" id="statStudents"><?= (int)$studentCount ?></div>
            <div class="bento-desc">Enrolled across all colleges</div>
        </div>

        <div class="bento-tile bento-tile-tall">
            <div class="bento-label">Faculty</div>
            <div class="bento-number" id="statTeachers"><?= (int)$teacherCount ?></div>
            <div class="bento-desc">Active teaching staff</div>
        </div>

        <div class="bento-tile">
            <div class="bento-label">Subjects Offered</div>
            <div class="bento-number" id="statSubjects" style="font-size:2rem;"><?= (int)$subjectCount ?></div>
        </div>

        <div class="bento-tile">
            <div class="bento-label">Pending Grades</div>
            <div class="bento-number" id="statPending" style="font-size:2rem;"><?= (int)$pendingCount ?></div>
        </div>

        <div class="bento-tile bento-tile-wide">
            <div>
                <div class="bento-title">Manage Users</div>
                <div class="bento-desc">Create and review admin, teacher, and student accounts.</div>
            </div>
            <a href="<?= site_url('admin/users') ?>" class="btn btn-university btn-sm align-self-start">Go to Users</a>
        </div>

        <div class="bento-tile bento-tile-wide">
            <div>
                <div class="bento-title">Manage Subjects</div>
                <div class="bento-desc">Add subjects, assign faculty, enroll students.</div>
            </div>
            <a href="<?= site_url('admin/subjects') ?>" class="btn btn-university btn-sm align-self-start">Go to Subjects</a>
        </div>

        <div class="bento-tile bento-tile-full">
            <div>
                <div class="bento-title">Audit Trail</div>
                <div class="bento-desc">Full logged history from the first entry, hash-chain verified, auto-refreshing.</div>
            </div>
            <a href="<?= site_url('admin/audit-trail') ?>" class="btn btn-university btn-sm align-self-start">View Audit Trail</a>
        </div>

    </div>
</div>

<script>
// FIX: AJAX polling - refresh the bento stat tiles and hero alert tile
// every 5 seconds, no page reload needed.
async function pollDashboard() {
    try {
        const res = await fetch('<?= site_url('admin/dashboard/poll') ?>', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        if (!res.ok) return;
        const data = await res.json();

        document.getElementById('statStudents').textContent = data.studentCount;
        document.getElementById('statTeachers').textContent = data.teacherCount;
        document.getElementById('statSubjects').textContent = data.subjectCount;
        document.getElementById('statPending').textContent = data.pendingCount;

        const hero = document.getElementById('heroTile');
        const label = document.getElementById('heroLabel');
        const number = document.getElementById('heroNumber');
        const desc = document.getElementById('heroDesc');

        number.textContent = data.highSeverityCount;
        if (data.highSeverityCount > 0) {
            hero.classList.remove('bento-tile-dark');
            hero.classList.add('bento-tile-alert');
            label.textContent = 'Security Alert';
            desc.textContent = 'high-severity security alert(s) in the last 24 hours (brute-force lockouts, honeytoken triggers).';
        } else {
            hero.classList.remove('bento-tile-alert');
            hero.classList.add('bento-tile-dark');
            label.textContent = 'All Clear';
            desc.textContent = 'No high-severity security alerts in the last 24 hours.';
        }
    } catch (e) {
        // silently skip - retry on next interval
    }
}

setInterval(pollDashboard, 5000);
</script>

<?= view('layouts/footer') ?>
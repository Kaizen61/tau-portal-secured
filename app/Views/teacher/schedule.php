<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <p class="text-muted">Your weekly teaching schedule.</p>
    <div class="card portal-card">
        <div class="card-body p-0">
            <table class="table table-portal table-striped mb-0">
                <thead><tr><th>Subject</th><th>Day</th><th>Time</th><th>Room</th></tr></thead>
                <tbody>
                    <tr><td>WST301 — Web Systems and Technologies</td><td>Mon / Wed</td><td>8:00 AM – 9:30 AM</td><td>IT Lab 2</td></tr>
                    <tr><td>IM201 — Information Management 2</td><td>Tue / Thu</td><td>10:00 AM – 11:30 AM</td><td>Room 204</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= view('layouts/footer') ?>

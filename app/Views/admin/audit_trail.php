<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
        <p class="text-muted mb-0">Searchable, integrity-verified portal activity.</p>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-university text-nowrap" data-bs-toggle="modal" data-bs-target="#databaseDownloadModal">Download Database (.sql)</button>
            <a id="printLogsButton" href="<?= site_url('admin/audit-trail/print') . '?' . http_build_query(array_filter(['action' => $filterAction, 'severity' => $filterSeverity, 'q_user' => $searchUser, 'q_ip' => $searchIp, 'q_ua' => $searchUa], static fn ($value): bool => $value !== null && $value !== '')) ?>" target="_blank" class="btn btn-sm btn-university text-nowrap">Print Logs</a>
            <a href="<?= site_url('admin/audit-trail/export') ?>?action=<?= esc($filterAction) ?>&severity=<?= esc($filterSeverity) ?>&q_user=<?= esc($searchUser) ?>&q_ip=<?= esc($searchIp) ?>&q_ua=<?= esc($searchUa) ?>" class="btn btn-sm btn-university text-nowrap">Export as JSON</a>
        </div>
    </div>

    <div class="card portal-card mb-3">
        <div class="card-body py-3" id="integrityBox">
            <?php if ($integrityOk): ?>
                <span class="badge bg-success">Log Integrity: Verified</span>
                <span class="text-muted small ms-2">The hash chain is intact - no past entry has been altered.</span>
            <?php else: ?>
                <span class="badge bg-danger">Log Integrity: BROKEN</span>
                <span class="text-muted small ms-2"><?= esc($integrityError) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="card portal-card">
        <div class="card-header bg-white">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-2">
                <h5 class="mb-0">System Activity Logs (<span id="rowCount"><?= count($logs) ?></span> entries)</h5>
                <div class="d-flex gap-2 flex-wrap">
                    <select name="action" id="filterAction" class="form-select form-select-sm" style="width:auto;">
                        <option value="">All Actions</option>
                        <?php foreach ($allowedActions as $a): ?>
                            <option value="<?= $a ?>" <?= $filterAction === $a ? 'selected' : '' ?>><?= $a ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="severity" id="filterSeverity" class="form-select form-select-sm" style="width:auto;">
                        <option value="">All Severities</option>
                        <option value="INFO" <?= $filterSeverity === 'INFO' ? 'selected' : '' ?>>Info</option>
                        <option value="WARNING" <?= $filterSeverity === 'WARNING' ? 'selected' : '' ?>>Warning</option>
                        <option value="HIGH" <?= $filterSeverity === 'HIGH' ? 'selected' : '' ?>>High</option>
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <input type="text" id="searchUser" class="form-control form-control-sm" style="max-width:180px;" placeholder="Search user..." value="<?= esc($searchUser) ?>">
                <input type="text" id="searchIp" class="form-control form-control-sm" style="max-width:180px;" placeholder="Search IP..." value="<?= esc($searchIp) ?>">
                <input type="text" id="searchUa" class="form-control form-control-sm" style="max-width:220px;" placeholder="Search User-Agent..." value="<?= esc($searchUa) ?>">
                <button type="button" id="clearFilters" class="btn btn-sm btn-university clear-filter-button">Clear</button>
            </div>
        </div>
        <div class="card-body p-0 audit-scroll-area">
            <table class="table table-portal table-striped mb-0">
            <thead><tr><th style="min-width:110px;">Date & Time</th><th>User</th><th>Action</th><th>Severity</th><th>Details</th><th>IP</th><th style="min-width:350px;">User-Agent</th></tr></thead>                
              <tbody id="logsTableBody">
                <?php foreach ($logs as $row): ?>
                    <?php $sev = $row['severity']; $badgeClass = $sev === 'HIGH' ? 'bg-danger' : ($sev === 'WARNING' ? 'bg-warning text-dark' : 'bg-secondary'); ?>
                    <?php $whenParts = explode(' ', $row['created_at'] ?? '', 2); ?>
                    <tr>
                        <td class="small">
                            <div><?= esc($whenParts[0] ?? '') ?></div>
                            <div class="text-muted"><?= esc($whenParts[1] ?? '') ?></div>
                        </td>
                        <td class="small"><?= esc($row['username'] ?? '—') ?></td>
                        <td class="small"><?= esc($row['action']) ?></td>
                        <td><span class="badge <?= $badgeClass ?>"><?= esc($sev) ?></span></td>
                        <td class="small"><?= esc($row['details'] ?? '') ?></td>
                        <td class="small"><?= esc($row['ip_address'] ?? '') ?></td>
                        <td class="small" style="min-width:350px; word-break:break-word; white-space:normal;"><?= esc($row['user_agent'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="databaseDownloadModal" tabindex="-1" aria-labelledby="databaseDownloadModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="databaseDownloadModalTitle">Download Database Backup?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>This SQL backup contains the full portal database, including personal records.</p>
                <p class="mb-0"><strong>Keep the downloaded file secure</strong> and share it only with authorized personnel.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="<?= site_url('admin/audit-trail/download-database') ?>" class="btn btn-university">Download Backup</a>
            </div>
        </div>
    </div>
</div>

<script {csp-script-nonce}>
function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    const div = document.createElement('div');
    div.textContent = str;
    return div.innerHTML;
}

function severityBadgeClass(sev) {
    if (sev === 'HIGH') return 'bg-danger';
    if (sev === 'WARNING') return 'bg-warning text-dark';
    return 'bg-secondary';
}

function buildPollUrl() {
    const params = new URLSearchParams();
    const action = document.getElementById('filterAction').value;
    const severity = document.getElementById('filterSeverity').value;
    const qUser = document.getElementById('searchUser').value.trim();
    const qIp = document.getElementById('searchIp').value.trim();
    const qUa = document.getElementById('searchUa').value.trim();
    if (action) params.set('action', action);
    if (severity) params.set('severity', severity);
    if (qUser) params.set('q_user', qUser);
    if (qIp) params.set('q_ip', qIp);
    if (qUa) params.set('q_ua', qUa);
    params.set('_', Date.now());
    return '<?= site_url('admin/audit-trail/poll') ?>?' + params.toString();
}

function updatePrintLogsUrl() {
    const params = new URLSearchParams();
    [
        ['action', document.getElementById('filterAction').value],
        ['severity', document.getElementById('filterSeverity').value],
        ['q_user', document.getElementById('searchUser').value.trim()],
        ['q_ip', document.getElementById('searchIp').value.trim()],
        ['q_ua', document.getElementById('searchUa').value.trim()],
    ].forEach(([key, value]) => {
        if (value) params.set(key, value);
    });
    document.getElementById('printLogsButton').href =
        '<?= site_url('admin/audit-trail/print') ?>' + (params.size ? '?' + params.toString() : '');
}

function renderIntegrity(ok, error) {
    const box = document.getElementById('integrityBox');
    if (ok) {
        box.innerHTML = '<span class="badge bg-success">Log Integrity: Verified</span>' +
            '<span class="text-muted small ms-2">The hash chain is intact - no past entry has been altered.</span>';
    } else {
        box.innerHTML = '<span class="badge bg-danger">Log Integrity: BROKEN</span>' +
            '<span class="text-muted small ms-2">' + escapeHtml(error) + '</span>';
    }
}

function renderLogs(logs) {
    const tbody = document.getElementById('logsTableBody');
    let html = '';
logs.forEach(function (row) {
    const whenParts = (row.created_at || '').split(' ');
    const whenDate = whenParts[0] || '';
    const whenTime = whenParts[1] || '';
    html += '<tr>' +
        '<td class="small"><div>' + escapeHtml(whenDate) + '</div><div class="text-muted">' + escapeHtml(whenTime) + '</div></td>' +
            '<td class="small">' + escapeHtml(row.username || '—') + '</td>' +
            '<td class="small">' + escapeHtml(row.action) + '</td>' +
            '<td><span class="badge ' + severityBadgeClass(row.severity) + '">' + escapeHtml(row.severity) + '</span></td>' +
            '<td class="small">' + escapeHtml(row.details || '') + '</td>' +
            '<td class="small">' + escapeHtml(row.ip_address || '') + '</td>' +
            '<td class="small" style="min-width:380px; word-break:break-all; white-space:normal;">' + escapeHtml(row.user_agent || '') + '</td>' +            '</tr>';
    });
    tbody.innerHTML = html;
    document.getElementById('rowCount').textContent = logs.length;
}

async function pollAuditTrail() {
    try {
        const res = await fetch(buildPollUrl(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            cache: 'no-store'
        });
        if (!res.ok) return;
        const data = await res.json();
        renderIntegrity(data.integrityOk, data.integrityError);
        renderLogs(data.logs);
    } catch (e) {
        // silently skip - retry on next interval
    }
}

let debounceTimer;
function triggerImmediatePoll() {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(pollAuditTrail, 300);
}

document.getElementById('filterAction').addEventListener('change', pollAuditTrail);
document.getElementById('filterSeverity').addEventListener('change', pollAuditTrail);
document.getElementById('filterAction').addEventListener('change', updatePrintLogsUrl);
document.getElementById('filterSeverity').addEventListener('change', updatePrintLogsUrl);
document.getElementById('searchUser').addEventListener('input', () => { triggerImmediatePoll(); updatePrintLogsUrl(); });
document.getElementById('searchIp').addEventListener('input', () => { triggerImmediatePoll(); updatePrintLogsUrl(); });
document.getElementById('searchUa').addEventListener('input', () => { triggerImmediatePoll(); updatePrintLogsUrl(); });
document.getElementById('clearFilters').addEventListener('click', function () {
    document.getElementById('filterAction').value = '';
    document.getElementById('filterSeverity').value = '';
    document.getElementById('searchUser').value = '';
    document.getElementById('searchIp').value = '';
    document.getElementById('searchUa').value = '';
    updatePrintLogsUrl();
    pollAuditTrail();
});

setInterval(pollAuditTrail, 5000);
</script>

<?= view('layouts/footer') ?>
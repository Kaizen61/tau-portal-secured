<?= view('layouts/header', get_defined_vars()) ?>

<div>
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
        <p class="text-muted mb-0">Searchable, integrity-verified portal activity.</p>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a id="printLogsButton" href="<?= site_url('admin/audit-trail/print') ?>" target="_blank" class="btn btn-sm btn-university text-nowrap">Print Logs</a>
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
                    <select name="action" id="filterAction" class="form-select form-select-sm audit-filter-select">
                        <option value="">All Actions</option>
                        <?php foreach ($allowedActions as $a): ?>
                            <option value="<?= $a ?>" <?= $filterAction === $a ? 'selected' : '' ?>><?= $a ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select name="severity" id="filterSeverity" class="form-select form-select-sm audit-filter-select">
                        <option value="">All Severities</option>
                        <option value="INFO" <?= $filterSeverity === 'INFO' ? 'selected' : '' ?>>Info</option>
                        <option value="WARNING" <?= $filterSeverity === 'WARNING' ? 'selected' : '' ?>>Warning</option>
                        <option value="HIGH" <?= $filterSeverity === 'HIGH' ? 'selected' : '' ?>>High</option>
                    </select>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <input type="text" id="searchUser" class="form-control form-control-sm audit-search-user" placeholder="Search user..." value="<?= esc($searchUser) ?>">
                <input type="text" id="searchIp" class="form-control form-control-sm audit-search-ip" placeholder="<?= $searchIp !== null && $searchIp !== '' ? 'IP filter active (value hidden)' : 'Search IP...' ?>" value="">
                <input type="text" id="searchUa" class="form-control form-control-sm audit-search-agent" placeholder="Search User-Agent..." value="<?= esc($searchUa) ?>">
                <button type="button" id="clearFilters" class="btn btn-sm btn-university clear-filter-button">Clear</button>
            </div>
        </div>
        <div class="card-body p-0 audit-scroll-area">
            <table class="table table-portal table-striped mb-0">
            <thead><tr><th class="audit-date-column">Date &amp; Time</th><th>User</th><th>Action</th><th>Severity</th><th>Details</th><th>IP</th><th class="audit-agent-column">User-Agent</th></tr></thead>
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
                        <td class="small audit-agent-cell"><?= esc($row['user_agent'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script {csp-script-nonce}>
function severityBadgeClass(sev) {
    if (sev === 'HIGH') return 'bg-danger';
    if (sev === 'WARNING') return 'bg-warning text-dark';
    return 'bg-secondary';
}

function createTextCell(value, className = 'small') {
    const cell = document.createElement('td');
    cell.className = className;
    cell.textContent = value === null || value === undefined ? '' : String(value);
    return cell;
}

function buildPollUrl() {
    const params = new URLSearchParams();
    const action = document.getElementById('filterAction').value;
    const severity = document.getElementById('filterSeverity').value;
    const qUser = document.getElementById('searchUser').value.trim();
    const qIp = document.getElementById('searchIp').value.trim() || activeIpFilter;
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
        ['q_ip', document.getElementById('searchIp').value.trim() || activeIpFilter],
        ['q_ua', document.getElementById('searchUa').value.trim()],
    ].forEach(([key, value]) => {
        if (value) params.set(key, value);
    });
    document.getElementById('printLogsButton').href =
        '<?= site_url('admin/audit-trail/print') ?>' + (params.size ? '?' + params.toString() : '');
}

let activeIpFilter = new URLSearchParams(window.location.search).get('q_ip') || '';

function renderIntegrity(ok, error) {
    const box = document.getElementById('integrityBox');
    const badge = document.createElement('span');
    const message = document.createElement('span');
    message.className = 'text-muted small ms-2';

    if (ok) {
        badge.className = 'badge bg-success';
        badge.textContent = 'Log Integrity: Verified';
        message.textContent = 'The hash chain is intact - no past entry has been altered.';
    } else {
        badge.className = 'badge bg-danger';
        badge.textContent = 'Log Integrity: BROKEN';
        message.textContent = error === null || error === undefined ? '' : String(error);
    }

    box.replaceChildren(badge, message);
}

function renderLogs(logs) {
    const tbody = document.getElementById('logsTableBody');
    const rows = document.createDocumentFragment();

    logs.forEach(function (row) {
        const whenParts = String(row.created_at || '').split(' ');
        const dateCell = document.createElement('td');
        const date = document.createElement('div');
        const time = document.createElement('div');
        dateCell.className = 'small';
        date.textContent = whenParts[0] || '';
        time.className = 'text-muted';
        time.textContent = whenParts[1] || '';
        dateCell.append(date, time);

        const severityCell = document.createElement('td');
        const severityBadge = document.createElement('span');
        severityBadge.className = 'badge ' + severityBadgeClass(row.severity);
        severityBadge.textContent = row.severity === null || row.severity === undefined
            ? ''
            : String(row.severity);
        severityCell.appendChild(severityBadge);

        const tr = document.createElement('tr');
        tr.append(
            dateCell,
            createTextCell(row.username || '—'),
            createTextCell(row.action),
            severityCell,
            createTextCell(row.details || ''),
            createTextCell(row.ip_address || ''),
            createTextCell(row.user_agent || '', 'small audit-agent-cell')
        );
        rows.appendChild(tr);
    });

    tbody.replaceChildren(rows);
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
document.getElementById('searchIp').addEventListener('input', function () {
    activeIpFilter = this.value.trim();
    triggerImmediatePoll();
    updatePrintLogsUrl();
});
document.getElementById('searchUa').addEventListener('input', () => { triggerImmediatePoll(); updatePrintLogsUrl(); });
document.getElementById('clearFilters').addEventListener('click', function () {
    document.getElementById('filterAction').value = '';
    document.getElementById('filterSeverity').value = '';
    document.getElementById('searchUser').value = '';
    document.getElementById('searchIp').value = '';
    activeIpFilter = '';
    document.getElementById('searchUa').value = '';
    updatePrintLogsUrl();
    pollAuditTrail();
});

updatePrintLogsUrl();
setInterval(pollAuditTrail, 5000);
</script>

<?= view('layouts/footer') ?>
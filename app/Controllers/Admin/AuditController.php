<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ActivityLogModel;
use App\Libraries\ActivityLogger;

class AuditController extends BaseController
{
    protected array $allowedActions = ['LOGIN_SUCCESS','LOGIN_FAILED','LOGIN_BLOCKED_LOCKOUT','HONEYTOKEN_TRIGGERED',
        'SUSPICIOUS_ACTIVITY','ACCESS_DENIED','ACCOUNT_CREATED','SUBJECT_CREATED','STUDENT_ENROLLED',
        'GRADE_CREATED','GRADE_UPDATED','TEACHER_EVALUATION_SUBMITTED','ANNOUNCEMENT_CREATED','ANNOUNCEMENT_UPDATED','ANNOUNCEMENT_DELETED',
        'LOGOUT','PROFILE_UPDATED','PASSWORD_CHANGED','DATABASE_EXPORTED'];
    protected array $allowedSeverities = ['INFO','WARNING','HIGH'];

    protected function sanitizeAuditRows(array $rows): array
    {
        foreach ($rows as &$row) {
            foreach ($row as $field => $value) {
                if ($field === 'ip_address' || $field === 'prev_hash' || $field === 'entry_hash') {
                    unset($row[$field]);
                    continue;
                }
                if (is_string($value)) {
                    $sanitizedValue = preg_replace_callback(
                        '/(?<![\d.])(?:\d{1,3}\.){3}\d{1,3}(?![\d.])/',
                        static function (array $matches): string {
                            $ip = $matches[0];
                            $isValidIp = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
                            return $isValidIp ? '[REDACTED]' : $ip;
                        },
                        $value
                    );
                    if ($sanitizedValue === null) {
                        throw new \RuntimeException('Unable to redact IP addresses from audit output.');
                    }
                    $row[$field] = $sanitizedValue;
                }
            }
        }
        unset($row);

        return $rows;
    }

    protected function buildFilteredQuery(ActivityLogModel $logs)
    {
        $action = $this->request->getGet('action');
        $severity = $this->request->getGet('severity');
        // FIX: search filters by user and User-Agent. CI4's like()
        // auto-escapes the value (parameterized), safe against injection
        // even though these are free-text search boxes.
        $searchUser = trim((string) $this->request->getGet('q_user'));
        $searchUa = trim((string) $this->request->getGet('q_ua'));

        // FIX per instruction: latest entry on top. Full history is still
        // shown (no row limit) - scroll down to reach the earliest entry.
        $builder = $logs->orderBy('id', 'DESC');
        if (in_array($action, $this->allowedActions, true)) $builder->where('action', $action);
        if (in_array($severity, $this->allowedSeverities, true)) $builder->where('severity', $severity);
        if ($searchUser !== '') $builder->like('username', $searchUser);
        if ($searchUa !== '') $builder->like('user_agent', $searchUa);

        return $builder;
    }

    public function index()
    {
        $logs = new ActivityLogModel();
        $logger = new ActivityLogger();
        [$integrityOk, $integrityError] = $logger->verifyChain();
        $rows = $this->buildFilteredQuery($logs)->findAll();

        $data = [
            'pageTitle' => 'System Activity Logs - TAU Student Portal',
            'activePage' => 'audit',
            'eyebrow' => 'Administration',
            'pageHeading' => 'System Activity Logs',
            'logs' => $this->sanitizeAuditRows($rows),
            'filterAction' => $this->request->getGet('action'),
            'filterSeverity' => $this->request->getGet('severity'),
            'searchUser' => $this->request->getGet('q_user'),
            'searchUa' => $this->request->getGet('q_ua'),
            'allowedActions' => $this->allowedActions,
            'integrityOk' => $integrityOk,
            'integrityError' => $integrityError,
        ];
        return view('admin/audit_trail', $data);
    }

    public function poll()
    {
        $logs = new ActivityLogModel();
        $logger = new ActivityLogger();
        [$integrityOk, $integrityError] = $logger->verifyChain();

        $rows = $this->sanitizeAuditRows($this->buildFilteredQuery($logs)->findAll());
        $json = json_encode([
            'integrityOk' => $integrityOk,
            'integrityError' => $integrityError,
            'count' => count($rows),
            'logs' => $rows,
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);

        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setHeader('Pragma', 'no-cache')
            ->setContentType('application/json')
            ->setBody($json);
    }

    public function printLogs()
    {
        $rows = $this->sanitizeAuditRows(
            $this->buildFilteredQuery(new ActivityLogModel())->findAll()
        );
        $action = $this->request->getGet('action');
        $severity = $this->request->getGet('severity');

        return view('admin/print_activity_logs', [
            'printTitle' => 'System Activity Logs - TAU Student Portal',
            'documentTitle' => 'System Activity Logs',
            'printOrientation' => 'landscape',
            'printedAt' => date('F j, Y g:i A'),
            'autoPrint' => false,
            'logs' => $rows,
            'filters' => array_filter([
                'Action' => in_array($action, $this->allowedActions, true) ? $action : '',
                'Severity' => in_array($severity, $this->allowedSeverities, true) ? $severity : '',
                'User' => trim((string) $this->request->getGet('q_user')),
                'User-Agent' => trim((string) $this->request->getGet('q_ua')),
            ]),
        ]);
    }
}
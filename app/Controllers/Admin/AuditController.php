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
            if (!empty($row['ip_address'])) {
                $row['ip_address'] = '[REDACTED]';
            }
            if (isset($row['details']) && is_string($row['details'])) {
                $sanitizedDetails = preg_replace_callback(
                    '/(?<![\d.])(?:\d{1,3}\.){3}\d{1,3}(?![\d.])/',
                    static function (array $matches): string {
                        $ip = $matches[0];
                        $isValidIp = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
                        $isPublicIp = filter_var(
                            $ip,
                            FILTER_VALIDATE_IP,
                            FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE
                        ) !== false;

                        return $isValidIp && !$isPublicIp ? '[REDACTED]' : $ip;
                    },
                    $row['details']
                );
                if ($sanitizedDetails === null) {
                    throw new \RuntimeException('Unable to redact private IP addresses from audit details.');
                }
                $row['details'] = $sanitizedDetails;
            }
            unset($row['prev_hash'], $row['entry_hash']);
        }
        unset($row);

        return $rows;
    }

    protected function buildFilteredQuery(ActivityLogModel $logs)
    {
        $action = $this->request->getGet('action');
        $severity = $this->request->getGet('severity');
        // FIX: search filters by user, IP, and User-Agent. CI4's like()
        // auto-escapes the value (parameterized), safe against injection
        // even though these are free-text search boxes.
        $searchUser = trim((string) $this->request->getGet('q_user'));
        $searchIp = trim((string) $this->request->getGet('q_ip'));
        $searchUa = trim((string) $this->request->getGet('q_ua'));

        // FIX per instruction: latest entry on top. Full history is still
        // shown (no row limit) - scroll down to reach the earliest entry.
        $builder = $logs->orderBy('id', 'DESC');
        if (in_array($action, $this->allowedActions, true)) $builder->where('action', $action);
        if (in_array($severity, $this->allowedSeverities, true)) $builder->where('severity', $severity);
        if ($searchUser !== '') $builder->like('username', $searchUser);
        if ($searchIp !== '') $builder->like('ip_address', $searchIp);
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
            'searchIp' => $this->request->getGet('q_ip'),
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

        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setHeader('Pragma', 'no-cache')
            ->setJSON([
                'integrityOk' => $integrityOk,
                'integrityError' => $integrityError,
                'count' => count($rows),
                'logs' => $rows,
            ]);
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
                'IP' => trim((string) $this->request->getGet('q_ip')) !== '' ? '[REDACTED]' : '',
                'User-Agent' => trim((string) $this->request->getGet('q_ua')),
            ]),
        ]);
    }
}
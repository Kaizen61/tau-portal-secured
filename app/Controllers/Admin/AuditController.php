<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ActivityLogModel;
use App\Libraries\ActivityLogger;

class AuditController extends BaseController
{
    protected array $allowedActions = ['LOGIN_SUCCESS','LOGIN_FAILED','LOGIN_BLOCKED_LOCKOUT','HONEYTOKEN_TRIGGERED',
        'SUSPICIOUS_ACTIVITY','ACCESS_DENIED','ACCOUNT_CREATED','SUBJECT_CREATED','STUDENT_ENROLLED',
        'GRADE_CREATED','GRADE_UPDATED','LOGOUT','PROFILE_UPDATED','PASSWORD_CHANGED'];
    protected array $allowedSeverities = ['INFO','WARNING','HIGH'];

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

        $data = [
            'pageTitle' => 'Audit Trail - TAU Student Portal',
            'activePage' => 'audit',
            'eyebrow' => 'Administration',
            'pageHeading' => 'Audit Trail',
            'logs' => $this->buildFilteredQuery($logs)->findAll(),
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

        $rows = $this->buildFilteredQuery($logs)->findAll();

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

    public function export()
    {
        $logs = new ActivityLogModel();
        $rows = $this->buildFilteredQuery($logs)->findAll();

        return $this->response
            ->setHeader('Content-Type', 'application/json')
            ->setHeader('Content-Disposition', 'attachment; filename="audit_log_export.json"')
            ->setBody(json_encode([
                'exported_at' => date('c'),
                'count' => count($rows),
                'logs' => $rows,
            ], JSON_PRETTY_PRINT));
    }
}
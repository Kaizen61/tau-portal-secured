<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ActivityLogModel;
use App\Libraries\ActivityLogger;

class AuditController extends BaseController
{
    protected array $allowedActions = ['LOGIN_SUCCESS','LOGIN_FAILED','LOGIN_BLOCKED_LOCKOUT','HONEYTOKEN_TRIGGERED',
        'SUSPICIOUS_ACTIVITY','ACCESS_DENIED','ACCOUNT_CREATED','SUBJECT_CREATED','STUDENT_ENROLLED',
        'GRADE_CREATED','GRADE_UPDATED','ANNOUNCEMENT_CREATED','ANNOUNCEMENT_UPDATED','ANNOUNCEMENT_DELETED',
        'LOGOUT','PROFILE_UPDATED','PASSWORD_CHANGED','DATABASE_EXPORTED'];
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
            'pageTitle' => 'System Activity Logs - TAU Student Portal',
            'activePage' => 'audit',
            'eyebrow' => 'Administration',
            'pageHeading' => 'System Activity Logs',
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

    public function downloadDatabase()
    {
        $db = db_connect();
        if ($db->getPlatform() !== 'MySQLi') {
            throw new \RuntimeException('Database downloads are only supported for MySQL databases.');
        }

        $tables = $db->listTables();
        $dump = "-- TAU Student Portal database export\n"
            . '-- Generated at ' . date('Y-m-d H:i:s') . "\n"
            . "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $escapedTable = $db->escapeIdentifiers($table);
            $createRow = $db->query('SHOW CREATE TABLE ' . $escapedTable)->getRowArray();
            $createStatement = $createRow['Create Table'] ?? array_values($createRow ?? [])[1] ?? null;
            if (!is_string($createStatement)) {
                throw new \RuntimeException('Unable to read the schema for a database table.');
            }

            $dump .= 'DROP TABLE IF EXISTS ' . $escapedTable . ";\n"
                . $createStatement . ";\n";

            $fields = $db->getFieldNames($table);
            $fieldTypes = [];
            foreach ($db->getFieldData($table) as $field) {
                $fieldTypes[$field->name] = strtolower((string) $field->type);
            }
            $escapedFields = implode(', ', array_map(
                static fn (string $field): string => $db->escapeIdentifiers($field),
                $fields
            ));

            $rows = $db->query('SELECT * FROM ' . $escapedTable)->getResultArray();
            foreach ($rows as $row) {
                $values = [];
                foreach ($fields as $field) {
                    $value = $row[$field] ?? null;
                    if ($value === null) {
                        $values[] = 'NULL';
                        continue;
                    }
                    if (is_resource($value)) {
                        $value = stream_get_contents($value);
                    }
                    $type = $fieldTypes[$field] ?? '';
                    $isBinary = str_contains($type, 'blob')
                        || str_contains($type, 'binary')
                        || str_contains($type, 'bit');
                    $values[] = $isBinary
                        ? '0x' . bin2hex((string) $value)
                        : $db->escape($value);
                }
                $dump .= 'INSERT INTO ' . $escapedTable . ' (' . $escapedFields . ') VALUES ('
                    . implode(', ', $values) . ");\n";
            }
            $dump .= "\n";
        }

        $dump .= "SET FOREIGN_KEY_CHECKS=1;\n";
        (new ActivityLogger())->log(
            (int) session()->get('user_id'),
            (string) session()->get('username'),
            'DATABASE_EXPORTED',
            'Downloaded SQL backup containing ' . count($tables) . ' tables',
            'INFO'
        );

        $databaseName = preg_replace('/[^A-Za-z0-9_-]/', '_', $db->getDatabase());
        $fileName = $databaseName . '_backup_' . date('Ymd_His') . '.sql';

        return $this->response
            ->setHeader('Content-Type', 'application/sql; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate')
            ->setHeader('Pragma', 'no-cache')
            ->setBody($dump);
    }

    public function printLogs()
    {
        $rows = $this->buildFilteredQuery(new ActivityLogModel())->findAll();
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
                'IP' => trim((string) $this->request->getGet('q_ip')),
                'User-Agent' => trim((string) $this->request->getGet('q_ua')),
            ]),
        ]);
    }
}
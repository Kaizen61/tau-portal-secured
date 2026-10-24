<?php

namespace App\Libraries;

use App\Models\ActivityLogModel;

class ActivityLogger
{
    protected ActivityLogModel $model;

    public function __construct()
    {
        $this->model = new ActivityLogModel();
    }

    /**
     * FIX (A09): every sensitive action is written here with full context
     * (user, action, details, IP, User-Agent, timestamp), and chained via
     * SHA-256 hash to the previous entry so tampering is detectable.
     */
    public function log(?int $userId, ?string $username, string $action, ?string $details = null, string $severity = 'INFO'): bool
    {
        $request = service('request');
        $ip = $request->getIPAddress();
        $userAgent = substr((string) $request->getUserAgent(), 0, 255);

        $last = $this->model->orderBy('id', 'DESC')->first();
        $prevHash = $last['entry_hash'] ?? str_repeat('0', 64);

        $payload = implode('|', [$userId, $username, $action, $details, $severity, $ip, $userAgent, $prevHash]);
        $entryHash = hash('sha256', $payload);

        return $this->model->insert([
            'user_id' => $userId,
            'username' => $username,
            'action' => $action,
            'details' => $details,
            'severity' => $severity,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'prev_hash' => $prevHash,
            'entry_hash' => $entryHash,
            'created_at' => date('Y-m-d H:i:s'),
        ]) !== false;
    }

    /**
     * Re-walks the whole chain in insertion order and recomputes each
     * hash. Returns [true, null] if intact, or [false, "row #id ..."] at
     * the first row where the stored hash no longer matches - meaning
     * that row (or an earlier one) was altered after the fact.
     */
    public function verifyChain(): array
    {
        $rows = $this->model->orderBy('id', 'ASC')->findAll();
        $prevHash = str_repeat('0', 64);

        foreach ($rows as $row) {
            if ($row['prev_hash'] !== $prevHash) {
                return [false, "Chain broken at log entry #{$row['id']} (prev_hash mismatch)."];
            }
            $payload = implode('|', [
                $row['user_id'], $row['username'], $row['action'], $row['details'],
                $row['severity'], $row['ip_address'], $row['user_agent'], $row['prev_hash'],
            ]);
            $recomputed = hash('sha256', $payload);
            if ($recomputed !== $row['entry_hash']) {
                return [false, "Chain broken at log entry #{$row['id']} (entry_hash mismatch - this row was likely edited)."];
            }
            $prevHash = $row['entry_hash'];
        }

        return [true, null];
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityLogModel extends Model
{
    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'user_id', 'username', 'action', 'details', 'severity',
        'ip_address', 'user_agent', 'prev_hash', 'entry_hash', 'created_at',
    ];

    public function recentFailedLoginCount(string $identifier, int $minutes = 5): int
    {
        return $this->where('action', 'LOGIN_FAILED')
            ->where('username', $identifier)
            ->where('created_at >=', date('Y-m-d H:i:s', time() - $minutes * 60))
            ->countAllResults();
    }

    public function recentHighSeverityCount(int $hours = 24): int
    {
        return $this->where('severity', 'HIGH')
            ->where('created_at >=', date('Y-m-d H:i:s', time() - $hours * 3600))
            ->countAllResults();
    }
}

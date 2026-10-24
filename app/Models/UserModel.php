<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $skipValidation = true;
    protected $allowedFields = [
        'username', 'student_number', 'employee_id', 'password', 'role', 'full_name',
        'college', 'program', 'year_level', 'section', 'failed_login_count', 'locked_until', 'is_active', 'created_at',
    ];

    public function findByUsername(string $username)
    {
        return $this->where('username', $username)->first();
    }

    public function findByStudentNumber(string $studentNumber)
    {
        return $this->where('student_number', $studentNumber)->where('role', 'student')->first();
    }

    public function identifierExists(string $field, string $value, ?int $excludeId = null): bool
    {
        if (!in_array($field, ['username', 'student_number'], true)) {
            throw new \InvalidArgumentException('Unsupported user identifier field.');
        }

        $sql = 'SELECT id FROM ' . $this->db->escapeIdentifiers($this->table)
            . ' WHERE LOWER(TRIM(' . $this->db->escapeIdentifiers($field) . ')) = ?';
        $params = [mb_strtolower(trim($value))];
        if ($excludeId !== null) {
            $sql .= ' AND id != ?';
            $params[] = $excludeId;
        }
        $sql .= ' LIMIT 1';

        return $this->db->query($sql, $params)->getNumRows() > 0;
    }

    public function hasActiveStatusColumn(): bool
    {
        return $this->db->fieldExists('is_active', $this->table);
    }

    public function whereActive()
    {
        if ($this->hasActiveStatusColumn()) {
            $this->where('is_active', 1);
        }

        return $this;
    }

    /**
     * FIX (A09/A10): real, persistent account lockout - 5 consecutive
     * failed attempts locks the account for a full hour, tracked directly
     * on the users row (not a rolling log-count window that resets and
     * lets an attacker back in every few minutes).
     *
     * Returns true if this failure just triggered a new lockout.
     */
    public function registerFailedLogin(int $userId, int $threshold, int $lockoutMinutes): bool
    {
        $user = $this->find($userId);
        $newCount = ((int) ($user['failed_login_count'] ?? 0)) + 1;

        if ($newCount >= $threshold) {
            $this->update($userId, [
                'failed_login_count' => 0,
                'locked_until' => date('Y-m-d H:i:s', time() + $lockoutMinutes * 60),
            ]);
            return true;
        }

        $this->update($userId, ['failed_login_count' => $newCount]);
        return false;
    }

    /**
     * Called on every SUCCESSFUL login - resets the streak. This is what
     * makes the count "consecutive": a correct password at any point
     * clears prior failures instead of them accumulating forever.
     */
    public function clearFailedLogin(int $userId): void
    {
        $this->update($userId, ['failed_login_count' => 0, 'locked_until' => null]);
    }

    public function isLocked(array $user): bool
    {
        if (empty($user['locked_until'])) {
            return false;
        }
        return strtotime($user['locked_until']) > time();
    }
}
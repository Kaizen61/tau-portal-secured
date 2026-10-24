<?php

namespace App\Models;

use CodeIgniter\Model;
use RuntimeException;

class EmployeeNumberSequenceModel extends Model
{
    protected $table = 'employee_number_sequence';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $skipValidation = true;
    protected $allowedFields = ['last_number'];

    public function nextNumberPreview(): string
    {
        $row = $this->find(1);
        if (!$row) {
            throw new RuntimeException('Employee number sequence is not initialized. Run the database migrations.');
        }

        return (string) ((int) $row['last_number'] + 1);
    }

    public function allocateNextNumber(): string
    {
        $row = $this->db->query(
            'SELECT last_number FROM employee_number_sequence WHERE id = 1 FOR UPDATE'
        )->getRowArray();
        if (!$row) {
            throw new RuntimeException('Employee number sequence is not initialized. Run the database migrations.');
        }

        $nextNumber = (int) $row['last_number'] + 1;
        if (strlen((string) $nextNumber) > 10) {
            throw new RuntimeException('The employee number sequence has reached its 10-digit limit.');
        }
        if (!$this->update(1, ['last_number' => $nextNumber])) {
            throw new RuntimeException('Could not reserve the next employee number.');
        }

        return (string) $nextNumber;
    }
}

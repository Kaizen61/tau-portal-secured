<?php

namespace App\Models;

use CodeIgniter\Model;
use RuntimeException;

class StudentNumberSequenceModel extends Model
{
    protected $table = 'student_number_sequence';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $skipValidation = true;
    protected $allowedFields = ['last_number'];

    public function nextNumberPreview(): string
    {
        $row = $this->find(1);
        if (!$row) {
            throw new RuntimeException('Student number sequence is not initialized. Run the database migrations.');
        }

        return $this->formatNumber((int) $row['last_number'] + 1);
    }

    public function allocateNextNumber(): string
    {
        $row = $this->db->query(
            'SELECT last_number FROM student_number_sequence WHERE id = 1 FOR UPDATE'
        )->getRowArray();
        if (!$row) {
            throw new RuntimeException('Student number sequence is not initialized. Run the database migrations.');
        }

        $nextNumber = (int) $row['last_number'] + 1;
        $formattedNumber = $this->formatNumber($nextNumber);
        if (!$this->update(1, ['last_number' => $nextNumber])) {
            throw new RuntimeException('Could not reserve the next student number.');
        }

        return $formattedNumber;
    }

    private function formatNumber(int $number): string
    {
        if ($number < 100 || $number > 999) {
            throw new RuntimeException('The student number sequence has reached its 3-digit limit.');
        }

        return '2023100' . str_pad((string) $number, 3, '0', STR_PAD_LEFT);
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

class ChangeAccountNumberYearTo2023 extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('users') || !$this->db->tableExists('employee_number_sequence')) {
            throw new RuntimeException('User and employee number tables must exist before account numbers can be updated.');
        }

        $sequence = $this->db->table('employee_number_sequence')->where('id', 1)->get()->getRowArray();
        if (!$sequence) {
            throw new RuntimeException('Employee number sequence is not initialized.');
        }

        $this->db->transBegin();

        $this->db->query(
            "UPDATE users SET student_number = CONCAT('2023', SUBSTRING(student_number, 5)) "
            . "WHERE role = 'student' AND student_number LIKE '2024%'"
        );
        $this->db->query(
            "UPDATE users SET employee_id = CONCAT('2023', SUBSTRING(employee_id, 5)) "
            . "WHERE role IN ('teacher', 'admin') AND employee_id LIKE '2024%'"
        );

        $lastNumber = (string) $sequence['last_number'];
        if (str_starts_with($lastNumber, '2024')) {
            $this->db->table('employee_number_sequence')
                ->where('id', 1)
                ->update(['last_number' => (int) ('2023' . substr($lastNumber, 4))]);
        }

        if (!$this->db->transStatus()) {
            $this->db->transRollback();
            throw new RuntimeException('Could not update existing student and employee number years.');
        }

        $this->db->transCommit();
    }

    public function down()
    {
        if ($this->db->tableExists('users')) {
            $this->db->query(
                "UPDATE users SET student_number = CONCAT('2024', SUBSTRING(student_number, 5)) "
                . "WHERE role = 'student' AND student_number LIKE '2023%'"
            );
            $this->db->query(
                "UPDATE users SET employee_id = CONCAT('2024', SUBSTRING(employee_id, 5)) "
                . "WHERE role IN ('teacher', 'admin') AND employee_id LIKE '2023%'"
            );
        }

        if ($this->db->tableExists('employee_number_sequence')) {
            $this->db->query(
                "UPDATE employee_number_sequence SET last_number = CONCAT('2024', SUBSTRING(CAST(last_number AS CHAR), 5)) "
                . "WHERE id = 1 AND CAST(last_number AS CHAR) LIKE '2023%'"
            );
        }
    }
}

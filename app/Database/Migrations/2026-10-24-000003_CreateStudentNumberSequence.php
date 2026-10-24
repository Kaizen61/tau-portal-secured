<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

class CreateStudentNumberSequence extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('users')) {
            throw new RuntimeException('The users table must exist before the student number sequence is initialized.');
        }

        if (!$this->db->tableExists('student_number_sequence')) {
            $this->forge->addField([
                'id' => ['type' => 'TINYINT', 'constraint' => 1, 'unsigned' => true],
                'last_number' => ['type' => 'SMALLINT', 'constraint' => 3, 'unsigned' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('student_number_sequence');
        }

        if ($this->db->table('student_number_sequence')->where('id', 1)->countAllResults() > 0) {
            return;
        }

        $students = $this->db->table('users')
            ->select('id')
            ->where('role', 'student')
            ->orderBy('student_number', 'ASC')
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        if (count($students) > 780) {
            throw new RuntimeException('There are too many students for the requested 3-digit student number sequence.');
        }

        $this->db->transBegin();

        $this->db->table('users')
            ->where('role', 'student')
            ->update(['student_number' => null]);

        foreach ($students as $index => $student) {
            $this->db->table('users')
                ->where('id', $student['id'])
                ->update(['student_number' => '2024100' . str_pad((string) (220 + $index), 3, '0', STR_PAD_LEFT)]);
        }

        $lastNumber = 219 + count($students);
        $this->db->table('student_number_sequence')->insert([
            'id' => 1,
            'last_number' => $lastNumber,
        ]);

        if (!$this->db->transStatus()) {
            $this->db->transRollback();
            throw new RuntimeException('Could not normalize existing student numbers.');
        }

        $this->db->transCommit();
    }

    public function down()
    {
        $this->forge->dropTable('student_number_sequence', true);
    }
}

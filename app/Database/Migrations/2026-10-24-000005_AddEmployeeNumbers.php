<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

class AddEmployeeNumbers extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('users')) {
            throw new RuntimeException('The users table must exist before employee numbers can be initialized.');
        }

        if (!$this->db->fieldExists('employee_id', 'users')) {
            $this->forge->addColumn('users', [
                'employee_id' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true, 'after' => 'student_number'],
            ]);
        }

        if (!$this->db->tableExists('employee_number_sequence')) {
            $this->forge->addField([
                'id' => ['type' => 'TINYINT', 'constraint' => 1, 'unsigned' => true],
                'last_number' => ['type' => 'BIGINT', 'unsigned' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('employee_number_sequence');
        }

        if ($this->db->table('employee_number_sequence')->where('id', 1)->countAllResults() === 0) {
            $employees = $this->db->table('users')
                ->select('id')
                ->whereIn('role', ['teacher', 'admin'])
                ->where('username !=', 'backup_admin')
                ->orderBy('id', 'ASC')
                ->get()
                ->getResultArray();

            $this->db->transBegin();
            foreach ($employees as $index => $employee) {
                $this->db->table('users')
                    ->where('id', $employee['id'])
                    ->update(['employee_id' => (string) (2024001100 + $index)]);
            }

            $this->db->table('employee_number_sequence')->insert([
                'id' => 1,
                'last_number' => 2024001099 + count($employees),
            ]);

            if (!$this->db->transStatus()) {
                $this->db->transRollback();
                throw new RuntimeException('Could not assign employee numbers to existing staff accounts.');
            }
            $this->db->transCommit();
        }

        $indexes = $this->db->getIndexData('users');
        $hasUniqueEmployeeIndex = false;
        foreach ($indexes as $index) {
            if (($index->type ?? '') === 'UNIQUE' && ($index->fields ?? []) === ['employee_id']) {
                $hasUniqueEmployeeIndex = true;
                break;
            }
        }
        if (!$hasUniqueEmployeeIndex) {
            $this->forge->addUniqueKey('employee_id');
            $this->forge->processIndexes('users');
        }
    }

    public function down()
    {
        $this->forge->dropTable('employee_number_sequence', true);
        if ($this->db->tableExists('users') && $this->db->fieldExists('employee_id', 'users')) {
            $this->forge->dropColumn('users', 'employee_id');
        }
    }
}

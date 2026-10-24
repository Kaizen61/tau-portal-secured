<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ScopeTeacherEvaluationsToSchoolYear extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('school_year', 'teacher_evaluations')) {
            $this->forge->addColumn('teacher_evaluations', [
                'school_year' => ['type' => 'VARCHAR', 'constraint' => 9, 'null' => true, 'after' => 'teacher_id'],
            ]);
        }

        $this->db->query(
            "UPDATE teacher_evaluations
            SET school_year = CASE
                WHEN MONTH(submitted_at) >= 6
                    THEN CONCAT(YEAR(submitted_at), '-', YEAR(submitted_at) + 1)
                ELSE CONCAT(YEAR(submitted_at) - 1, '-', YEAR(submitted_at))
            END
            WHERE school_year IS NULL"
        );

        $this->forge->modifyColumn('teacher_evaluations', [
            'school_year' => ['type' => 'VARCHAR', 'constraint' => 9, 'null' => false, 'after' => 'teacher_id'],
        ]);

        $indexes = $this->db->getIndexData('teacher_evaluations');
        $singleColumnIndexes = [];
        foreach ($indexes as $index) {
            if (($index->type ?? '') !== 'PRIMARY' && count($index->fields ?? []) === 1) {
                $singleColumnIndexes[$index->fields[0]] = true;
            }
        }
        foreach (['student_id', 'teacher_id'] as $field) {
            if (!isset($singleColumnIndexes[$field])) {
                $this->forge->addKey($field, false, false, 'idx_teacher_evaluations_' . $field);
            }
        }
        $this->forge->processIndexes('teacher_evaluations');

        $indexes = $this->db->getIndexData('teacher_evaluations');
        $hasCurrentUniqueKey = false;
        foreach ($indexes as $index) {
            $fields = $index->fields ?? [];
            if (($index->type ?? '') === 'UNIQUE' && $fields === ['student_id', 'teacher_id']) {
                $this->forge->dropKey('teacher_evaluations', $index->name);
            }
            if (($index->type ?? '') === 'UNIQUE' && $fields === ['student_id', 'teacher_id', 'school_year']) {
                $hasCurrentUniqueKey = true;
            }
        }

        if (!$hasCurrentUniqueKey) {
            $this->forge->addUniqueKey(
                ['student_id', 'teacher_id', 'school_year'],
                'uq_teacher_eval_student_teacher_year'
            );
            $this->forge->processIndexes('teacher_evaluations');
        }
    }

    public function down()
    {
        $indexes = $this->db->getIndexData('teacher_evaluations');
        foreach ($indexes as $index) {
            if (($index->type ?? '') === 'UNIQUE'
                && ($index->fields ?? []) === ['student_id', 'teacher_id', 'school_year']) {
                $this->forge->dropKey('teacher_evaluations', $index->name);
            }
        }

        $this->forge->addUniqueKey(
            ['student_id', 'teacher_id'],
            'uq_teacher_eval_student_teacher'
        );
        $this->forge->processIndexes('teacher_evaluations');
        $this->forge->dropColumn('teacher_evaluations', 'school_year');
    }
}

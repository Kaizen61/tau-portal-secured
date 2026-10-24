<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ScopeTeacherEvaluationsToSemester extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('semester', 'teacher_evaluations')) {
            $this->forge->addColumn('teacher_evaluations', [
                'semester' => [
                    'type' => 'VARCHAR',
                    'constraint' => 3,
                    'default' => '1st',
                    'null' => false,
                    'after' => 'school_year',
                ],
            ]);
        }

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
        $hasSemesterUniqueKey = false;
        foreach ($indexes as $index) {
            $fields = $index->fields ?? [];
            if (($index->type ?? '') === 'UNIQUE' && $fields === ['student_id', 'teacher_id', 'school_year']) {
                $this->forge->dropKey('teacher_evaluations', $index->name);
            }
            if (($index->type ?? '') === 'UNIQUE'
                && $fields === ['student_id', 'teacher_id', 'school_year', 'semester']) {
                $hasSemesterUniqueKey = true;
            }
        }

        if (!$hasSemesterUniqueKey) {
            $this->forge->addUniqueKey(
                ['student_id', 'teacher_id', 'school_year', 'semester'],
                'uq_teacher_eval_student_teacher_term'
            );
            $this->forge->processIndexes('teacher_evaluations');
        }
    }

    public function down()
    {
        $indexes = $this->db->getIndexData('teacher_evaluations');
        foreach ($indexes as $index) {
            if (($index->type ?? '') === 'UNIQUE'
                && ($index->fields ?? []) === ['student_id', 'teacher_id', 'school_year', 'semester']) {
                $this->forge->dropKey('teacher_evaluations', $index->name);
            }
        }

        $this->forge->addUniqueKey(
            ['student_id', 'teacher_id', 'school_year'],
            'uq_teacher_eval_student_teacher_year'
        );
        $this->forge->processIndexes('teacher_evaluations');
        $this->forge->dropColumn('teacher_evaluations', 'semester');
    }
}

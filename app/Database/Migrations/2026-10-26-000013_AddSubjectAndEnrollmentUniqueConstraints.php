<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSubjectAndEnrollmentUniqueConstraints extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('subjects') || !$this->db->tableExists('grades')) {
            throw new \RuntimeException('Subjects and grades tables must exist before adding duplicate-prevention constraints.');
        }

        $duplicateChecks = [
            'subjects' => [
                'SELECT year_level, semester, LOWER(TRIM(subject_code)) AS duplicate_value, COUNT(*) AS total
                 FROM subjects
                 GROUP BY year_level, semester, LOWER(TRIM(subject_code))
                 HAVING COUNT(*) > 1 LIMIT 1',
                'SELECT year_level, semester, LOWER(TRIM(subject_name)) AS duplicate_value, COUNT(*) AS total
                 FROM subjects
                 GROUP BY year_level, semester, LOWER(TRIM(subject_name))
                 HAVING COUNT(*) > 1 LIMIT 1',
            ],
            'grades' => [
                'SELECT student_id, subject_id, school_year, COUNT(*) AS total
                 FROM grades
                 GROUP BY student_id, subject_id, school_year
                 HAVING COUNT(*) > 1 LIMIT 1',
            ],
        ];

        foreach ($duplicateChecks as $table => $queries) {
            foreach ($queries as $query) {
                if ($this->db->query($query)->getRowArray() !== null) {
                    throw new \RuntimeException(
                        "Cannot add duplicate-prevention constraints: existing duplicate data was found in {$table}. Resolve duplicates without deleting academic records, then rerun migrations."
                    );
                }
            }
        }

        $this->addUniqueIndexIfMissing('subjects', ['year_level', 'semester', 'subject_code'], 'uq_subject_year_semester_code');
        $this->addUniqueIndexIfMissing('subjects', ['year_level', 'semester', 'subject_name'], 'uq_subject_year_semester_name');
        $this->addUniqueIndexIfMissing('grades', ['student_id', 'subject_id', 'school_year'], 'uq_grade_student_subject_year');
    }

    public function down()
    {
        $this->dropUniqueIndexIfPresent('subjects', ['year_level', 'semester', 'subject_code']);
        $this->dropUniqueIndexIfPresent('subjects', ['year_level', 'semester', 'subject_name']);
        $this->dropUniqueIndexIfPresent('grades', ['student_id', 'subject_id', 'school_year']);
    }

    private function addUniqueIndexIfMissing(string $table, array $fields, string $name): void
    {
        foreach ($this->db->getIndexData($table) as $index) {
            if (($index->type ?? '') === 'UNIQUE' && ($index->fields ?? []) === $fields) {
                return;
            }
        }

        $this->forge->addUniqueKey($fields, $name);
        $this->forge->processIndexes($table);
    }

    private function dropUniqueIndexIfPresent(string $table, array $fields): void
    {
        foreach ($this->db->getIndexData($table) as $index) {
            if (($index->type ?? '') === 'UNIQUE' && ($index->fields ?? []) === $fields) {
                $this->forge->dropKey($table, $index->name);
                $this->forge->processIndexes($table);
                return;
            }
        }
    }
}

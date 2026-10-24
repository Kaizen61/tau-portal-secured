<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTeacherEvaluationComments extends Migration
{
    public function up()
    {
        foreach ([
            'strengths' => 'What are the teacher strengths?',
            'improvements' => 'What improvements would you recommend?',
            'additional_comments' => 'Additional comments or suggestions',
        ] as $field => $comment) {
            if (!$this->db->fieldExists($field, 'teacher_evaluations')) {
                $this->forge->addColumn('teacher_evaluations', [
                    $field => ['type' => 'TEXT', 'null' => true, 'comment' => $comment],
                ]);
            }
        }
    }

    public function down()
    {
        foreach (['strengths', 'improvements', 'additional_comments'] as $field) {
            if ($this->db->fieldExists($field, 'teacher_evaluations')) {
                $this->forge->dropColumn('teacher_evaluations', $field);
            }
        }
    }
}

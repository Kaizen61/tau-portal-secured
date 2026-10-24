<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTeacherEvaluations extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('teacher_evaluations')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'student_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'teacher_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'submitted_at' => ['type' => 'DATETIME'],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['student_id', 'teacher_id'], 'uq_teacher_eval_student_teacher');
            $this->forge->addKey('teacher_id');
            $this->forge->addForeignKey('student_id', 'users', 'id', 'CASCADE', 'CASCADE', 'fk_teacher_eval_student');
            $this->forge->addForeignKey('teacher_id', 'users', 'id', 'CASCADE', 'CASCADE', 'fk_teacher_eval_teacher');
            $this->forge->createTable('teacher_evaluations');
        }

        if (!$this->db->tableExists('teacher_evaluation_scores')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'evaluation_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'criterion' => ['type' => 'VARCHAR', 'constraint' => 50],
                'score' => ['type' => 'TINYINT', 'constraint' => 1, 'unsigned' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['evaluation_id', 'criterion'], 'uq_teacher_eval_criterion');
            $this->forge->addForeignKey('evaluation_id', 'teacher_evaluations', 'id', 'CASCADE', 'CASCADE', 'fk_teacher_eval_score_evaluation');
            $this->forge->createTable('teacher_evaluation_scores');
        }
    }

    public function down()
    {
        $this->forge->dropTable('teacher_evaluation_scores', true);
        $this->forge->dropTable('teacher_evaluations', true);
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGrades extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('grades')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'student_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'subject_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
                'school_year' => ['type' => 'VARCHAR', 'constraint' => 9],
                'grade' => ['type' => 'DECIMAL', 'constraint' => '4,2', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('grades');
        }
    }

    public function down()
    {
        $this->forge->dropTable('grades');
    }
}

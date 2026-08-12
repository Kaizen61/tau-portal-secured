<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubjects extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'subject_code' => ['type' => 'VARCHAR', 'constraint' => 20],
            'subject_name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'units' => ['type' => 'INT', 'constraint' => 2, 'default' => 3],
            'year_level' => ['type' => 'TINYINT', 'constraint' => 1],
            'semester' => ['type' => 'ENUM', 'constraint' => ['1st', '2nd']],
            'teacher_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('subjects');
    }

    public function down()
    {
        $this->forge->dropTable('subjects');
    }
}

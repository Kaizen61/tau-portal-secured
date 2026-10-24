<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsers extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('users')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'username' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'student_number' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
                'password' => ['type' => 'VARCHAR', 'constraint' => 255],
                'role' => ['type' => 'ENUM', 'constraint' => ['student', 'teacher', 'admin']],
                'full_name' => ['type' => 'VARCHAR', 'constraint' => 100],
                'college' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'year_level' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
                'failed_login_count' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
                'locked_until' => ['type' => 'DATETIME', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('username');
            $this->forge->addUniqueKey('student_number');
            $this->forge->createTable('users');
        }
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}

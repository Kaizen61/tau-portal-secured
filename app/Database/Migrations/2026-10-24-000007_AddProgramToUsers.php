<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProgramToUsers extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('users') && !$this->db->fieldExists('program', 'users')) {
            $this->forge->addColumn('users', [
                'program' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true, 'after' => 'college'],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('users') && $this->db->fieldExists('program', 'users')) {
            $this->forge->dropColumn('users', 'program');
        }
    }
}

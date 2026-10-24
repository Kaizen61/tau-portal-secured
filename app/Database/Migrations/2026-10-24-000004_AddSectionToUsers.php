<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSectionToUsers extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('users') && !$this->db->fieldExists('section', 'users')) {
            $this->forge->addColumn('users', [
                'section' => ['type' => 'VARCHAR', 'constraint' => 1, 'null' => true, 'after' => 'year_level'],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('users') && $this->db->fieldExists('section', 'users')) {
            $this->forge->dropColumn('users', 'section');
        }
    }
}

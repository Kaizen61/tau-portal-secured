<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsActiveToUsers extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('users') && !$this->db->fieldExists('is_active', 'users')) {
            $this->forge->addColumn('users', [
                'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1, 'after' => 'locked_until'],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('users') && $this->db->fieldExists('is_active', 'users')) {
            $this->forge->dropColumn('users', 'is_active');
        }
    }
}

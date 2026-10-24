<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BackfillUserActiveStatus extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('users') && $this->db->fieldExists('is_active', 'users')) {
            $this->db->query('UPDATE users SET is_active = 1 WHERE is_active IS NULL');
        }
    }

    public function down()
    {
    }
}

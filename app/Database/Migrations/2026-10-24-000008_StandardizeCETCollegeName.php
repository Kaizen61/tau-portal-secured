<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class StandardizeCETCollegeName extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('users')) {
            $this->db->table('users')
                ->where('college', 'College of Information Technology')
                ->update(['college' => 'College of Engineering and Technology (CET)']);
        }
    }

    public function down()
    {
    }
}

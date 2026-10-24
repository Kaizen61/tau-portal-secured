<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SetExistingStudentsCollegeToCET extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('users')) {
            $this->db->table('users')
                ->where('role', 'student')
                ->update(['college' => 'College of Engineering and Technology (CET)']);
        }
    }

    public function down()
    {
    }
}

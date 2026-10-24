<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAnnouncements extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('announcements')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'title' => ['type' => 'VARCHAR', 'constraint' => 150],
                'message' => ['type' => 'TEXT'],
                'created_by' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'created_at' => ['type' => 'DATETIME'],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('announcements');
        }
    }

    public function down()
    {
        $this->forge->dropTable('announcements', true);
    }
}

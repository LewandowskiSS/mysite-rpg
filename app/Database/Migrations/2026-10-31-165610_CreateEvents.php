<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEvents extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'text' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'type' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],
            'min_value' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'max_value' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'chance' => [
                'type'       => 'TINYINT',
                'constraint' => 3,
                'unsigned'   => true,
                'default'    => 100,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('events');
    }

    public function down()
    {
        $this->forge->dropTable('events');
    }
}
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLastWalkAt extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'last_walk_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'last_walk_at');
    }
}

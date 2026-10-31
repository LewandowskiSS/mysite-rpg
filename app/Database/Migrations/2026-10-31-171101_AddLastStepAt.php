<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLastStepAt extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'last_step_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'last_step_at');
    }
}
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLastHpRegenAt extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'last_hp_regen_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'last_hp_regen_at');
    }
}

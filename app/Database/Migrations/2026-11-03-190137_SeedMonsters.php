<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedMonsters extends Migration
{
    public function up()
    {
        $monsters = [
            ['name' => 'Слизень',      'hp' => 20,  'attack' => 4,  'exp_reward' => 15, 'gold_reward' => 8,  'min_level' => 1,  'max_level' => 3],
            ['name' => 'Волк',         'hp' => 40,  'attack' => 7,  'exp_reward' => 30, 'gold_reward' => 15, 'min_level' => 4,  'max_level' => 6],
            ['name' => 'Гоблин',       'hp' => 70,  'attack' => 11, 'exp_reward' => 50, 'gold_reward' => 25, 'min_level' => 7,  'max_level' => 10],
            ['name' => 'Скелет-воин',  'hp' => 110, 'attack' => 16, 'exp_reward' => 80, 'gold_reward' => 40, 'min_level' => 11, 'max_level' => 15],
            ['name' => 'Тёмный рыцарь','hp' => 160, 'attack' => 22, 'exp_reward' => 120, 'gold_reward' => 60, 'min_level' => 16, 'max_level' => 99],
        ];

        foreach ($monsters as $m) {
            $this->db->table('monsters')->insert($m);
        }
    }

    public function down()
    {
        $this->db->table('monsters')->emptyTable();
    }
}
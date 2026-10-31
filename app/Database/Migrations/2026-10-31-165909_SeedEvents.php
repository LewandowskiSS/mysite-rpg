<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeedEvents extends Migration
{
    public function up()
    {
        $events = [
            [
                'name'      => 'find_gold',
                'text'      => 'Вы нашли мешочек с золотом у обочины.',
                'type'      => 'gold',
                'min_value' => 5,
                'max_value' => 25,
                'chance'    => 35,
            ],
            [
                'name'      => 'find_exp',
                'text'      => 'Старый свиток открыл вам новые знания.',
                'type'      => 'exp',
                'min_value' => 10,
                'max_value' => 40,
                'chance'    => 30,
            ],
            [
                'name'      => 'monster',
                'text'      => 'На вас напал дикий волк, но вы отбились.',
                'type'      => 'hp',
                'min_value' => -15,
                'max_value' => -5,
                'chance'    => 20,
            ],
            [
                'name'      => 'nothing',
                'text'      => 'Тихий вечер. Ничего примечательного не произошло.',
                'type'      => 'nothing',
                'min_value' => 0,
                'max_value' => 0,
                'chance'    => 15,
            ],
        ];

        foreach ($events as $event) {
            $this->db->table('events')->insert($event);
        }
    }

    public function down()
    {
        $this->db->table('events')->emptyTable();
    }
}
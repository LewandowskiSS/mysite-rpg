<?php

namespace App\Models;

use CodeIgniter\Model;

class Event extends Model
{
    protected $table         = 'events';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['name', 'text', 'type', 'min_value', 'max_value', 'chance'];
    protected $useTimestamps = false;

    // Pick a random event weighted by 'chance'.
    public function pickRandom()
    {
        $events = $this->findAll();

        $total = array_sum(array_column($events, 'chance'));
        $roll  = mt_rand(1, max(1, $total));

        foreach ($events as $event) {
            $roll -= $event['chance'];
            if ($roll <= 0) {
                return $event;
            }
        }

        return $events[0] ?? null;
    }
}
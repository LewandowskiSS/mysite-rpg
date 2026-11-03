<?php

namespace App\Models;

use CodeIgniter\Model;

class Monster extends Model
{
    protected $table         = 'monsters';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['name', 'hp', 'attack', 'exp_reward', 'gold_reward', 'min_level', 'max_level'];
    protected $useTimestamps = false;

    // Pick a random monster appropriate for the player's level.
    public function pickForLevel(int $level)
    {
        $monsters = $this->where('min_level <=', $level)
                         ->where('max_level >=', $level)
                         ->findAll();

        if (empty($monsters)) {
            // Fallback: closest tier if level is outside all ranges.
            $monsters = $this->orderBy('min_level', 'ASC')->findAll();
        }

        if (empty($monsters)) {
            return null;
        }

        return $monsters[array_rand($monsters)];
    }
}
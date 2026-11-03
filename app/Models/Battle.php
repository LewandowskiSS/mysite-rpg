<?php

namespace App\Models;

use CodeIgniter\Model;

class Battle extends Model
{
    protected $table         = 'battles';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['user_id', 'monster_id', 'monster_hp', 'token', 'status', 'last_attack_at', 'created_at', 'ended_at'];
    protected $useTimestamps = false;

    public function findActiveByUser(int $userId)
    {
        return $this->where('user_id', $userId)
                    ->where('status', 'active')
                    ->first();
    }
}
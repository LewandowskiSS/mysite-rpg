<?php

namespace App\Models;

use CodeIgniter\Model;

class User extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['username', 'email', 'password_hash', 'level', 'exp', 'gold', 'hp', 'energy', 'last_step_at'];

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = '';
    protected $deletedField     = '';

    public function findByUsername(string $username)
    {
        return $this->where('username', $username)->first();
    }
}
<?php

namespace App\Controllers;

use App\Models\User;
use CodeIgniter\Controller;

class Game extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function index()
    {
        if (! session()->get('user_id')) {
            return redirect()->to('login');
        }

        $user = $this->currentUser;

        if (! $user) {
            session()->destroy();
            return redirect()->to('login');
        }
        // Calculate remaining cooldown for the walk button (seconds).
        $cooldownRemaining = 0;
        if (! empty($user['last_walk_at'])) {
            $elapsed = time() - strtotime($user['last_walk_at']);
            $cooldownRemaining = max(0, \App\Controllers\Steps::COOLDOWN_SEC - $elapsed);
        }

        return view('game/dashboard', [
            'user'              => $user,
            'cooldownRemaining' => $cooldownRemaining,
        ]);
    }
}

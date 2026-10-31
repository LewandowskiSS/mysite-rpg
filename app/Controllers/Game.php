<?php

namespace App\Controllers;

use App\Models\User;
use CodeIgniter\Controller;

class Game extends Controller
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

        $user = $this->userModel->find(session()->get('user_id'));

        if (! $user) {
            session()->destroy();
            return redirect()->to('login');
        }

        return view('game/dashboard', ['user' => $user]);
    }
}

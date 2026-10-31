<?php

namespace App\Controllers;

use App\Models\User;
use CodeIgniter\Controller;

class Auth extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function register()
    {
        if (session()->get('user_id')) {
            return redirect()->to('/');
        }
        return view('auth/register');
    }

    public function attemptRegister()
    {
        if (session()->get('user_id')) {
          return redirect()->to('/');
        }

        $rules = [
            'username' => 'required|min_length[3]|max_length[30]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
        ];
      
        $username = trim($this->request->getPost('username'));
        $email    = trim($this->request->getPost('email'));
        $password = (string) $this->request->getPost('password');
        

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'username'      => filter_var($username, FILTER_SANITIZE_FULL_SPECIAL_CHARS),
            'email'         => filter_var($email, FILTER_SANITIZE_EMAIL),
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ];
      
        $this->userModel->insert($data);

        return redirect()->to('login')->with('success', 'Account created. Please login.');
    }

    public function login()
    {
        if (session()->get('user_id')) {
            return redirect()->to('/');
        }
        return view('auth/login');
    }

    public function attemptLogin()
    {
        if (session()->get('user_id')) {
          return redirect()->to('/');
        }
      
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
      
        $throttler = \Config\Services::throttler();
      
        if ($throttler->check(md5($this->request->getIPAddress() . 'login'), 5, MINUTE) === false) {
          return redirect()->back()->withInput()->with('error', 'Too many attempts. Try again later.');
        }
      
        $username = trim($this->request->getPost('username'));
        $user = $this->userModel->findByUsername($username);


        if ($user && password_verify($this->request->getPost('password'), $user['password_hash'])) {
            session()->set('user_id', $user['id']);
            session()->set('username', $user['username']);
            return redirect()->to('/');
        }

        return redirect()->back()->withInput()->with('error', 'Invalid username or password.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('login');
    }
}
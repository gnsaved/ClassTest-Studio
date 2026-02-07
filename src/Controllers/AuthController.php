<?php

namespace ClassTest\Controllers;

use ClassTest\Models\User;
use ClassTest\Helpers\Auth;

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
    }
    
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            
            $user = $this->userModel->findByEmail($email);
            
            if ($user && $this->userModel->verifyPassword($password, $user['password'])) {
                Auth::login($user);
                
                if ($user['role'] === 'admin') {
                    header('Location: /dashboard');
                } else {
                    header('Location: /student/assessments');
                }
                exit;
            } else {
                $error = 'Invalid credentials';
                require __DIR__ . '/../Views/auth/login.php';
            }
        } else {
            require __DIR__ . '/../Views/auth/login.php';
        }
    }
    
    public function logout() {
        Auth::logout();
        header('Location: /login');
        exit;
    }
}

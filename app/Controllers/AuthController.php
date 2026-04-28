<?php

namespace App\Controllers;

use App\Services\AuthService;

class AuthController extends BaseController
{
    private AuthService $authService;
    
    public function __construct()
    {
        $this->authService = new AuthService();
    }
    
    public function login()
    {
        if ($this->userId) {
            return redirect()->to('/dashboard');
        }
        
        return view('auth/login', [
            'csrf_token' => csrf_hash()
        ]);
    }
    
    public function doLogin()
    {
        if (!$this->isAjax()) {
            return $this->jsonResponse(['success' => false, 'message' => 'Invalid request']);
        }
        
        $email = trim($this->request->getPost('email'));
        $password = $this->request->getPost('password');
        
        if (empty($email) || empty($password)) {
            return $this->jsonResponse(['success' => false, 'message' => 'Email and password are required']);
        }
        
        $result = $this->authService->login($email, $password);
        
        if ($result['success']) {
            session()->set([
                'isLoggedIn' => true,
                'customer_id' => $result['user']['id'],
                'customer_name' => $result['user']['name'],
                'customer_email' => $result['user']['email']
            ]);
            
            return $this->jsonResponse([
                'success' => true,
                'redirect' => site_url('/dashboard')
            ]);
        }
        
        return $this->jsonResponse([
            'success' => false,
            'message' => $result['message']
        ]);
    }
    
    public function register()
    {
        if ($this->userId) {
            return redirect()->to('/dashboard');
        }
        
        return view('auth/register', [
            'csrf_token' => csrf_hash()
        ]);
    }
    
    public function doRegister()
    {
        if (!$this->isAjax()) {
            return $this->jsonResponse(['success' => false, 'message' => 'Invalid request']);
        }
        
        $data = [
            'name' => trim($this->request->getPost('name')),
            'email' => trim($this->request->getPost('email')),
            'password' => $this->request->getPost('password'),
            'confirm_password' => $this->request->getPost('confirm_password')
        ];
        
        if (!$this->authService->validateRegistrationData($data)) {
            return $this->jsonResponse([
                'success' => false,
                'errors' => $this->authService->getErrors()
            ]);
        }
        
        $result = $this->authService->register($data);
        
        if ($result['success']) {
            return $this->jsonResponse([
                'success' => true,
                'redirect' => site_url('/login')
            ]);
        }
        
        return $this->jsonResponse([
            'success' => false,
            'errors' => [$result['message']]
        ]);
    }
    
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
    
    public function dashboard()
    {
        if (!$this->userId) {
            return redirect()->to('/login');
        }
        
        return view('dashboard/index', [
            'title' => 'Dashboard',
            'activePage' => 'dashboard'
        ]);
    }
}
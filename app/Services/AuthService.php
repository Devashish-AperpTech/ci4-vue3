<?php

namespace App\Services;

use App\Repositories\UserRepository;

class AuthService extends BaseService
{
    private UserRepository $userRepository;
    
    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }
    
    public function login(string $email, string $password): array
    {
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user) {
            return $this->respondWithError('Invalid email or password');
        }
        
        if (!$this->userRepository->verifyPassword($password, $user['customer_password'])) {
            return $this->respondWithError('Invalid email or password');
        }
        
        return $this->respondWithSuccess('Login successful', [
            'user' => [
                'id' => $user['customer_id'],
                'name' => $user['customer_name'],
                'email' => $user['customer_email']
            ]
        ]);
    }
    
    public function register(array $data): array
    {
        $existingUser = $this->userRepository->findByEmail($data['email']);
        
        if ($existingUser) {
            return $this->respondWithError('Email already registered');
        }
        
        $userData = [
            'customer_name' => $data['name'],
            'customer_email' => $data['email'],
            'customer_password' => $data['password']
        ];
        
        $userId = $this->userRepository->createUser($userData);
        
        if (!$userId) {
            return $this->respondWithError('Registration failed');
        }
        
        return $this->respondWithSuccess('Registration successful', ['user_id' => $userId]);
    }
    
    public function validateRegistrationData(array $data): bool
    {
        $this->clearErrors();
        
        if (strlen($data['name'] ?? '') < 2) {
            $this->addError('Name must be at least 2 characters');
        }
        
        if (!filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            $this->addError('Valid email is required');
        }
        
        if (strlen($data['password'] ?? '') < 4) {
            $this->addError('Password must be at least 4 characters');
        }
        
        if (($data['password'] ?? '') !== ($data['confirm_password'] ?? '')) {
            $this->addError('Passwords do not match');
        }
        
        return !$this->hasErrors();
    }
}
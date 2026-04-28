<?php

namespace App\Repositories;

use App\Models\UserModel;

class UserRepository extends BaseRepository
{
    protected UserModel $userModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        parent::__construct($this->userModel);
    }
    
    public function findByEmail(string $email)
    {
        return $this->userModel->findByEmail($email);
    }
    
    public function createUser(array $userData)
    {
        return $this->userModel->insert($userData);
    }
    
    public function updateUser(int $userId, array $userData)
    {
        return $this->userModel->update($userId, $userData);
    }
    
    public function verifyPassword(string $plainPassword, string $hashedPassword): bool
    {
        return password_verify($plainPassword, $hashedPassword);
    }
    
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }
}
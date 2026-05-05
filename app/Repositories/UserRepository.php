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

    public function findById(int $userId)
    {
        return $this->userModel->find($userId);
    }

    public function getPaginatedCustomers(int $page, int $perPage, ?string $search = null): array
    {
        $offset = ($page - 1) * $perPage;
        $table = $this->userModel->getTable();

        $builder = $this->userModel->db->table($table . ' c')
            ->select('c.customer_id, c.customer_name, c.customer_email, c.customer_vat_code, c.customer_identifier_code, c.parent_id, c.created_at, p.customer_name as parent_name')
            ->join($table . ' p', 'p.customer_id = c.parent_id', 'left');

        if ($search !== null && $search !== '') {
            $builder->groupStart()
                ->like('c.customer_name', $search)
                ->orLike('c.customer_email', $search)
                ->orLike('c.customer_vat_code', $search)
                ->orLike('c.customer_identifier_code', $search)
                ->groupEnd();
        }

        $data = $builder->orderBy('c.customer_id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $countBuilder = $this->userModel->db->table($table . ' c');
        if ($search !== null && $search !== '') {
            $countBuilder->groupStart()
                ->like('c.customer_name', $search)
                ->orLike('c.customer_email', $search)
                ->orLike('c.customer_vat_code', $search)
                ->orLike('c.customer_identifier_code', $search)
                ->groupEnd();
        }
        $total = (int) $countBuilder->countAllResults();

        return [
            'data' => $data,
            'total' => $total,
        ];
    }
    
    public function createUser(array $userData)
    {
        return $this->userModel->insert($userData);
    }
    
    public function updateUser(int $userId, array $userData)
    {
        return $this->userModel->update($userId, $userData);
    }

    public function deleteUser(int $userId): bool
    {
        return (bool) $this->userModel->delete($userId);
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

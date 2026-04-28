<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'dgt_customers';
    protected $primaryKey = 'customer_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'customer_name',
        'customer_email',
        'customer_password',
        'customer_vat_code',
        'customer_identifier_code',
        'parent_id',
        'settings'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = '';
    
    protected $validationRules = [
        'customer_name' => 'required|min_length[2]',
        'customer_email' => 'required|valid_email|is_unique[dgt_customers.customer_email]',
        'customer_password' => 'required|min_length[4]'
    ];
    
    protected $validationMessages = [
        'customer_email' => [
            'is_unique' => 'This email is already registered.'
        ]
    ];
    
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];
    
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['customer_password'])) {
            $data['data']['customer_password'] = password_hash(
                $data['data']['customer_password'], 
                PASSWORD_DEFAULT
            );
        }
        return $data;
    }
    
    public function findByEmail($email)
    {
        return $this->where('customer_email', $email)->first();
    }
    
    public function verifyPassword($plainPassword, $hashedPassword)
    {
        return password_verify($plainPassword, $hashedPassword);
    }
}
<?php

namespace App\Models;

use CodeIgniter\Model;

class FormModel extends Model
{
    protected $table = 'user_forms';
    protected $primaryKey = 'form_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'customer_id',
        'title',
        'description',
        'form_data',
        'status'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[255]',
        'customer_id' => 'required|integer'
    ];
    
    // Get forms for specific user
    public function getUserForms($customerId)
    {
        return $this->where('customer_id', $customerId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }
    
    // Get single form with ownership check
    public function getUserForm($formId, $customerId)
    {
        return $this->where('form_id', $formId)
                    ->where('customer_id', $customerId)
                    ->first();
    }
    
    // Create form for user
    public function createForm($customerId, $data)
    {
        $data['customer_id'] = $customerId;
        return $this->insert($data);
    }
    
    // Update form with ownership check
    public function updateForm($formId, $customerId, $data)
    {
        $form = $this->getUserForm($formId, $customerId);
        if (!$form) {
            return false;
        }
        return $this->update($formId, $data);
    }
    
    // Delete form with ownership check
    public function deleteForm($formId, $customerId)
    {
        $form = $this->getUserForm($formId, $customerId);
        if (!$form) {
            return false;
        }
        return $this->delete($formId);
    }
}
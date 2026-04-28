<?php

namespace App\Repositories;

use App\Models\FormModel;

class FormRepository extends BaseRepository
{
    protected FormModel $formModel;
    
    public function __construct()
    {
        $this->formModel = new FormModel();
        parent::__construct($this->formModel);
    }
    
    public function getUserForms(int $customerId): array
    {
        return $this->formModel->getUserForms($customerId);
    }
    
    public function getUserForm(int $formId, int $customerId)
    {
        return $this->formModel->getUserForm($formId, $customerId);
    }
    
    public function createForm(int $customerId, array $data)
    {
        return $this->formModel->createForm($customerId, $data);
    }
    
    public function updateForm(int $formId, int $customerId, array $data): bool
    {
        return $this->formModel->updateForm($formId, $customerId, $data);
    }
    
    public function deleteForm(int $formId, int $customerId): bool
    {
        return $this->formModel->deleteForm($formId, $customerId);
    }
    
    public function getTotalFormsCount(int $customerId): int
    {
        return $this->formModel->where('customer_id', $customerId)->countAllResults();
    }
    
    public function getRecentForms(int $customerId, int $limit = 5): array
    {
        return $this->formModel->where('customer_id', $customerId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
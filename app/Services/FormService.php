<?php

namespace App\Services;

use App\Repositories\FormRepository;

class FormService extends BaseService
{
    private FormRepository $formRepository;
    
    public function __construct()
    {
        $this->formRepository = new FormRepository();
    }
    
    public function getUserForms(int $customerId): array
    {
        $forms = $this->formRepository->getUserForms($customerId);
        
        // Decode JSON form_data for each form
        foreach ($forms as &$form) {
            if (isset($form['form_data']) && is_string($form['form_data'])) {
                $form['form_data'] = json_decode($form['form_data'], true);
            }
        }
        
        return $forms;
    }
    
    public function getForm(int $formId, int $customerId)
    {
        $form = $this->formRepository->getUserForm($formId, $customerId);
        
        if ($form && isset($form['form_data']) && is_string($form['form_data'])) {
            $form['form_data'] = json_decode($form['form_data'], true);
        }
        
        return $form;
    }
    
    public function createForm(int $customerId, array $data): array
    {
        $validation = $this->validateFormData($data);
        if (!$validation['valid']) {
            return $this->respondWithError(implode(', ', $validation['errors']));
        }
        
        $formData = [
            'title' => trim($data['title']),
            'description' => trim($data['description'] ?? ''),
            'form_data' => $this->prepareFormData($data['form_data'] ?? null),
            'status' => 'submitted'
        ];
        
        $formId = $this->formRepository->createForm($customerId, $formData);
        
        if (!$formId) {
            return $this->respondWithError('Failed to save form');
        }
        
        return $this->respondWithSuccess('Form created successfully', ['form_id' => $formId]);
    }
    
    public function updateForm(int $formId, int $customerId, array $data): array
    {
        $existingForm = $this->formRepository->getUserForm($formId, $customerId);
        
        if (!$existingForm) {
            return $this->respondWithError('Form not found or access denied');
        }
        
        $validation = $this->validateFormData($data);
        if (!$validation['valid']) {
            return $this->respondWithError(implode(', ', $validation['errors']));
        }
        
        $formData = [
            'title' => trim($data['title']),
            'description' => trim($data['description'] ?? ''),
            'form_data' => $this->prepareFormData($data['form_data'] ?? null)
        ];
        
        $updated = $this->formRepository->update($formId, $formData);
        
        if (!$updated) {
            return $this->respondWithError('Failed to update form');
        }
        
        return $this->respondWithSuccess('Form updated successfully');
    }
    
    public function deleteForm(int $formId, int $customerId): array
    {
        $deleted = $this->formRepository->deleteForm($formId, $customerId);
        
        if (!$deleted) {
            return $this->respondWithError('Form not found or access denied');
        }
        
        return $this->respondWithSuccess('Form deleted successfully');
    }
    
    public function getDashboardData(int $customerId): array
    {
        $totalForms = $this->formRepository->getTotalFormsCount($customerId);
        $recentForms = $this->formRepository->getRecentForms($customerId, 5);
        
        // Decode JSON for recent forms
        foreach ($recentForms as &$form) {
            if (isset($form['form_data']) && is_string($form['form_data'])) {
                $form['form_data'] = json_decode($form['form_data'], true);
            }
        }
        
        return [
            'totalForms' => $totalForms,
            'recentActivity' => $totalForms,
            'recentForms' => $recentForms
        ];
    }
    
    private function validateFormData(array $data): array
    {
        $errors = [];
        
        if (empty(trim($data['title'] ?? ''))) {
            $errors[] = 'Title is required';
        }
        
        if (isset($data['form_data']) && !empty($data['form_data'])) {
            $jsonData = is_string($data['form_data']) ? $data['form_data'] : json_encode($data['form_data']);
            if (!empty($jsonData) && !json_decode($jsonData) && json_last_error() !== JSON_ERROR_NONE) {
                $errors[] = 'Invalid JSON format in form data';
            }
        }
        
        return ['valid' => empty($errors), 'errors' => $errors];
    }
    
    private function prepareFormData($data): ?string
    {
        if (empty($data)) {
            return null;
        }
        
        if (is_string($data)) {
            return $data;
        }
        
        return json_encode($data);
    }
}
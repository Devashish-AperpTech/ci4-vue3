<?php

namespace App\Controllers;

use App\Services\FormService;

class FormController extends BaseController
{
    private int $customerId;
    private FormService $formService;
    
    public function __construct()
    {
        if (!session()->get('isLoggedIn')) {
            redirect()->to('/login');
            exit();
        }
        $this->customerId = (int) session()->get('customer_id');
        $this->formService = new FormService();
    }
    
    // Page: List all forms
    public function index()
    {
        return view('forms/index', [
            'title' => 'Manage Forms',
            'activePage' => 'forms'
        ]);
    }
    
    // Page: Create form
    public function create()
    {
        return view('forms/form', [
            'title' => 'Create Form',
            'activePage' => 'forms',
            'mode' => 'create',
            'formId' => null,
            'formData' => [
                'title' => '',
                'description' => '',
                'form_data' => '{}'
            ]
        ]);
    }
    
    // Page: Edit form
    public function edit($formId)
    {
        $form = $this->formService->getForm((int) $formId, $this->customerId);
        
        if (!$form) {
            return redirect()->to('/forms')->with('error', 'Form not found');
        }
        
        return view('forms/form', [
            'title' => 'Edit Form',
            'activePage' => 'forms',
            'mode' => 'edit',
            'formId' => (int) $formId,
            'formData' => [
                'title' => $form['title'],
                'description' => $form['description'] ?? '',
                'form_data' => is_array($form['form_data']) 
                    ? json_encode($form['form_data'], JSON_PRETTY_PRINT) 
                    : ($form['form_data'] ?? '{}')
            ]
        ]);
    }
    
    // API: Get all forms for current user
    public function getData()
    {
        $forms = $this->formService->getUserForms($this->customerId);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $forms
        ]);
    }
    
    // API: Get single form
    public function getForm($formId)
    {
        $form = $this->formService->getForm((int) $formId, $this->customerId);
        
        if (!$form) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Form not found'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $form
        ]);
    }
    
    // API: Create new form
    public function save()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }
        
        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'form_data' => $this->request->getPost('form_data')
        ];
        
        $result = $this->formService->createForm($this->customerId, $data);
        
        if ($result['success']) {
            return $this->response->setJSON([
                'success' => true,
                'message' => $result['message'],
                'redirect' => site_url('/forms')
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'errors' => [$result['message']]
        ]);
    }
    
    // API: Update existing form
    public function update($formId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }
        
        $data = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'form_data' => $this->request->getPost('form_data')
        ];
        
        $result = $this->formService->updateForm((int) $formId, $this->customerId, $data);
        
        if ($result['success']) {
            return $this->response->setJSON([
                'success' => true,
                'message' => $result['message'],
                'redirect' => site_url('/forms')
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'errors' => [$result['message']]
        ]);
    }
    
    // API: Delete form
    public function delete($formId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }
        
        $result = $this->formService->deleteForm((int) $formId, $this->customerId);
        
        return $this->response->setJSON([
            'success' => $result['success'],
            'message' => $result['message']
        ]);
    }
}
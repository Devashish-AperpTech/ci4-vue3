<?php

namespace App\Controllers;

use App\Services\CustomerService;

class CustomerController extends BaseController
{
    private CustomerService $customerService;

    public function __construct()
    {
        $this->customerService = new CustomerService();
    }

    public function index()
    {
        if (!$this->userId) {
            return redirect()->to('/login');
        }

        return view('customers/index', [
            'title' => 'Customers',
            'activePage' => 'customers',
        ]);
    }

    public function getData()
    {
        if (!$this->userId) {
            return $this->jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('perPage') ?? 10);
        $search = (string) ($this->request->getGet('search') ?? '');

        return $this->jsonResponse($this->customerService->listCustomers($page, $perPage, $search));
    }

    public function create()
    {
        if (!$this->userId) {
            return $this->jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        if (!$this->isAjax()) {
            return $this->jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $payload = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'vat_code' => $this->request->getPost('vat_code'),
        ];

        $result = $this->customerService->createCustomer($payload, (int) $this->userId);
        return $this->jsonResponse($result, $result['success'] ? 200 : 422);
    }

    public function update(int $customerId)
    {
        if (!$this->userId) {
            return $this->jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        if (!$this->isAjax()) {
            return $this->jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $payload = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'vat_code' => $this->request->getPost('vat_code'),
            'customer_identifier_code' => $this->request->getPost('customer_identifier_code'),
        ];

        $result = $this->customerService->updateCustomer($customerId, $payload);
        return $this->jsonResponse($result, $result['success'] ? 200 : 422);
    }

    public function delete(int $customerId)
    {
        if (!$this->userId) {
            return $this->jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        if (!$this->isAjax()) {
            return $this->jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $result = $this->customerService->deleteCustomer($customerId, (int) $this->userId);
        return $this->jsonResponse($result, $result['success'] ? 200 : 422);
    }
}

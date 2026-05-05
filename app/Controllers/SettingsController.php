<?php

namespace App\Controllers;

use App\Services\SettingsService;

class SettingsController extends BaseController
{
    private SettingsService $settingsService;

    public function __construct()
    {
        $this->settingsService = new SettingsService();
    }

    public function index()
    {
        if (!$this->userId) {
            return redirect()->to('/login');
        }

        return view('settings/index', [
            'title' => 'Settings',
            'activePage' => 'settings',
        ]);
    }

    public function getData()
    {
        if (!$this->userId) {
            return $this->jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        return $this->jsonResponse($this->settingsService->getProfile((int) $this->userId));
    }

    public function updateProfile()
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
            'vat_code' => $this->request->getPost('vat_code'),
        ];

        $result = $this->settingsService->updateProfile((int) $this->userId, $payload);
        if ($result['success']) {
            session()->set([
                'customer_name' => trim((string) $payload['name']),
                'customer_email' => trim((string) $payload['email']),
            ]);
        }

        return $this->jsonResponse($result, $result['success'] ? 200 : 422);
    }

    public function updatePassword()
    {
        if (!$this->userId) {
            return $this->jsonResponse(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        if (!$this->isAjax()) {
            return $this->jsonResponse(['success' => false, 'message' => 'Invalid request'], 400);
        }

        $payload = [
            'current_password' => $this->request->getPost('current_password'),
            'new_password' => $this->request->getPost('new_password'),
            'confirm_password' => $this->request->getPost('confirm_password'),
        ];

        $result = $this->settingsService->updatePassword((int) $this->userId, $payload);
        return $this->jsonResponse($result, $result['success'] ? 200 : 422);
    }
}


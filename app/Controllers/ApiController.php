<?php

namespace App\Controllers;

use App\Services\FormService;

class ApiController extends BaseController
{
    private FormService $formService;
    
    public function __construct()
    {
        $this->requireLogin();
        $this->formService = new FormService();
    }
    
    public function dashboardData()
    {
        $data = $this->formService->getDashboardData($this->userId);
        
        return $this->jsonResponse([
            'success' => true,
            'data' => $data
        ]);
    }
}
<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    public function __construct()
    {
        $this->requireLogin();
    }
    
    public function index()
    {
        $uri = service('uri');
        $page = $uri->getSegment(1) ?? 'dashboard';
        
        return view('dashboard/index', [
            'title' => ucfirst($page),
            'activePage' => $page
        ]);
    }
}
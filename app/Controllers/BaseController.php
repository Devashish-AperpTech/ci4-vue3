<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

class BaseController extends Controller
{
    protected $helpers = ['url', 'form', 'session'];
    protected ?int $userId = null;
    
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);
        
        if (session()->get('isLoggedIn')) {
            $this->userId = (int) session()->get('customer_id');
        }
    }
    
    protected function requireLogin()
    {
        if (!$this->userId) {
            return redirect()->to('/login');
        }
    }
    
    protected function isAjax(): bool
    {
        return $this->request->isAJAX();
    }
    
    protected function jsonResponse($data, int $status = 200): ResponseInterface
    {
        return $this->response->setJSON($data)->setStatusCode($status);
    }
}
<?php

namespace Config;

$routes = Services::routes();

if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('AuthController');
$routes->setDefaultMethod('login');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

// Auth Routes
$routes->get('/', 'AuthController::login');
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::doLogin');
$routes->get('/register', 'AuthController::register');
$routes->post('/register', 'AuthController::doRegister');
$routes->get('/logout', 'AuthController::logout');
$routes->get('/dashboard', 'AuthController::dashboard');

// Form Routes
$routes->get('/forms', 'FormController::index');
$routes->get('/forms/create', 'FormController::create');
$routes->get('/forms/edit/(:num)', 'FormController::edit/$1');
$routes->get('/forms/getData', 'FormController::getData');
$routes->post('/forms/save', 'FormController::save');
$routes->post('/forms/update/(:num)', 'FormController::update/$1');
$routes->delete('/forms/delete/(:num)', 'FormController::delete/$1');

// Invoice Routes
$routes->get('/invoices', 'InvoiceController::index');
$routes->get('/invoices/create', 'InvoiceController::create');
$routes->get('/invoices/edit/(:num)', 'InvoiceController::edit/$1');

// API Routes
$routes->get('/api/dashboard-data', 'ApiController::dashboardData');
$routes->get('/api/invoices', 'InvoiceController::getData');
$routes->get('/api/invoices/(:num)', 'InvoiceController::getInvoice/$1');
$routes->post('/api/invoices', 'InvoiceController::save');
$routes->post('/api/invoices/update/(:num)', 'InvoiceController::update/$1');
$routes->delete('/api/invoices/(:num)', 'InvoiceController::delete/$1');

// Other modules
$routes->get('/customers', 'DashboardController::index');
$routes->get('/settings', 'DashboardController::index');

// customer routes
$routes->get('/customers', 'CustomerController::index');
$routes->get('/customers/getData', 'CustomerController::getData');
$routes->post('/customers/create', 'CustomerController::create');
$routes->post('/customers/update/(:num)', 'CustomerController::update/$1');
$routes->delete('/customers/delete/(:num)', 'CustomerController::delete/$1');

// setting routes
$routes->get('/settings', 'SettingsController::index');
$routes->get('/settings/getData', 'SettingsController::getData');
$routes->post('/settings/update-profile', 'SettingsController::updateProfile');
$routes->post('/settings/update-password', 'SettingsController::updatePassword');
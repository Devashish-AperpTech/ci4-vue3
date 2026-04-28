<?php

namespace App\Controllers;

use Config\Database;

class TestPG extends BaseController
{
    public function index()
    {
        try {
            $db = Database::connect();
            $result = $db->query("SELECT 1 as test");
            echo "✅ PostgreSQL connected successfully!";
        } catch (\Exception $e) {
            echo "❌ Connection failed: " . $e->getMessage();
        }
    }
}
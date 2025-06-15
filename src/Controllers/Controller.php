<?php

namespace App\Controllers;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

abstract class Controller
{
    protected $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function renderView($viewName, $data = []) {
        $viewPath = 'views/' . $viewName . '.php';
        if (file_exists($viewPath)) {
            extract($data);
            require $viewPath;
        } else {
            throw new \Exception("View '$viewName' not found.");
        }
    }
}
?>
<?php declare(strict_types=1);

namespace App\controllers;

require_once ROOT . '/functions.php';

class users
{
    public static function index(): void
    {
        view("users");
    }
}
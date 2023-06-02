<?php declare(strict_types=1);

namespace App\controllers;

require_once ROOT . '/functions.php';

class logout
{
    public static function index(): void
    {
        session_unset();
        session_destroy();
        session_regenerate_id(true);
        redirect("/");
    }
}
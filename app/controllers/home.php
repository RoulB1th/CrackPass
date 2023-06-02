<?php declare(strict_types=1);

namespace App\controllers;

require_once ROOT . '/functions.php';

class home
{
    public static function index(): void
    {
        view("home");
    }
    public static function register(): void
    {
        if(isset($_SESSION['isLogged'])){
            redirect("/");
        }
        view("register");
    }
    public static function test() : void 
    {
        echo "text";
    }
}
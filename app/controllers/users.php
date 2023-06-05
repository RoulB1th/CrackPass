<?php declare(strict_types=1);

namespace App\controllers;

use Core\database;

require_once ROOT . '/functions.php';

class users
{
    public static function index(): void
    {
        $conn = new database;
        $users = $conn->query("SELECT * FROM users ORDER BY games_posted DESC LIMIT 15")->fetchAllAssociative();
        $avatars = null;
        foreach($users as $user){
            $avatars = $conn->query("SELECT * FROM avatar WHERE is_games = 0")->fetchAllAssociative();
        }
        view("users",[
            "users"=>$users,
            "avatar"=>$avatars
        ]);
    }
}
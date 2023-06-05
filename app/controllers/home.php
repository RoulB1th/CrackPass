<?php declare(strict_types=1);

namespace App\controllers;

use Core\database;

require_once ROOT . '/functions.php';

class home
{
    public static function index(): void
    {
        $conn = new database;
        $games = $conn->query("SELECT * FROM games ORDER BY posted_at DESC LIMIT 9")->fetchAllAssociative();
        $avatars = null;
        foreach($games as $game){
            $id = $game["primary_picture_id"];
            $avatars = $conn->query("SELECT * FROM avatar WHERE is_games = 1")->fetchAllAssociative();
        }
        view("home",[
            "games"=>$games,
            "avatar"=>$avatars
        ]);
    }
    public static function login() : void 
    {
        if(isset($_SESSION['isLogged'])){
            redirect("/");
            return;
        }
        view("login");
    }
    public static function post() : void 
    {
        view("post");
    }
    public static function register(): void
    {
        if(isset($_SESSION['isLogged'])){
            redirect("/");
            return;
        }
        view("register");
    }
    public static function test() : void 
    {
        echo "text";
    }
}
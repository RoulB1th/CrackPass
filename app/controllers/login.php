<?php declare(strict_types=1);

namespace App\controllers;

use Core\database;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once ROOT . '/functions.php';
require_once ROOT . '/validator/validation.php';

class login
{
    public static function login(): void
    {
        $response = new Response();
        $request = Request::createFromGlobals();
        
        $username = $request->getPayload()->getString("username");
        $password = $request->getPayload()->getString("password");

        $errors = [];

        $attempts = rateLimit();
        if($attempts){
            $errors["limit"] = "Please Try Again Later";
            view("login",[
                "errors"=>$errors
            ]);
            return ;
        }

        if(!validateUsername($username)){
            $errors["username"] = "The Provided Username , Must at least be between 3-20 Characters And No Alpha Characters";
        }
        if(!validatePassword($password)) {
            $errors["password"] = "Invalid Password";
        };

        if(!empty($errors)){
            view("login",[
                "errors"=>$errors
            ]);
            return ;
        }

        $conn = new database;

        $user = $conn->query("SELECT * FROM users WHERE username=:username",[
            "username"=>$username
        ])->fetchAssociative();

        if(!$user){
            $errors["exist"] = "Invalid Credentials";
            view("login",[
                "errors"=>$errors
            ]);
            return;
        }
        
        $hashedPassword = $user["password"];

        if(!password_verify($password, $hashedPassword)){
            $errors["exist"] = "Invalid Credentials";
            view("login",[
                "errors"=>$errors
            ]);
            return;
        }

        $_SESSION['isLogged'] = true;
        $_SESSION['user'] = $username;

        redirect("/");

    }
}
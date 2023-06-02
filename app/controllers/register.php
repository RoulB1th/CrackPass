<?php declare(strict_types=1);

namespace App\controllers;

require_once ROOT . '/functions.php';
require_once ROOT . '/validator/validation.php';

define("MAX_SIZE", 26214400);

use Bulletproof\Image;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Core\database;

require_once ROOT . '/functions.php';

class register
{
    public static function register(): void
    {
        $response = new Response();
        $request = Request::createFromGlobals();

        $username = $request->getPayload()->getString("username");
        $password = $request->getPayload()->getString("password");
        $email = $request->getPayload()->getString("email");

        $errors = [];

        $image = new Image($_FILES);

        $db = new database;

        #In Case They Uploaded Their FIle :-)
        $avatarId = null;
        if(!validateUsername($username)){
            $errors["username"] = "The Provided Username , Must at least be between 3-20 Characters And No Alpha Characters";
        }
        if(!validatePassword($password)) {
            $errors["password"] = "Invalid Password";
        };
        if(!validateEmail($email)){
            $errors["email"] = "Invalid Email";
        }
        $users = $db->query("SELECT * FROM users WHERE username=':username'",[
            "username"=>$username
        ])->fetchAssociative();
        if($users){
            $errors['exist'] = "User Already Exists";
        }
        if($image["picture"]){
            $size = $image->getSize();
            if($size > MAX_SIZE){
                $errors['file'] = "A File Size Must Be Smaller Than 20MB";
            }
            $fileType = $image->getMime();
            if($fileType == "png" || $fileType == "gif" || $fileType == "jpg"){
                
            }else{
                $errors['file'] = "File Format Must Be PNG,JPG Or Gif";
            }
            if(empty($errors)){
                $path = 'images/'.date("dmyhms") . '/' . $image->getSize();
                mkdir($path,0775,true);
                $image->setStorage($path);
                $image->setName(date("hms"));
                $uri = 'images/'.date("dmyhms") . '/' . $image->getSize() . '/' . $image->getName() . '.' . $image->getMime();
                $image->upload();
                $db->query("INSERT INTO avatar(uri,is_games) VALUES(:uri, :is_games)",[
                    "uri"=>$uri,
                    "is_games"=>0
                ]);
                $avatar  = $db->query("SELECT * FROM avatar WHERE uri=:uri",[
                    "uri"=>$uri
                ]);
                $avatarId = $avatar->fetchAssociative()["id"];
            }else{
                view("register",[
                    "errors"=>$errors
                ]);
            }

        }else{
            if(empty($errors)){
                $avatar  = $db->query("SELECT * FROM avatar WHERE uri='images/GuestPFP.png'");
                $avatarId = $avatar->fetchAssociative()["id"];
            }else{
                view("register",[
                    "errors"=>$errors
                ]);
                die();
            }
           
        }
        
        #Creating User
        $default_gamesDownloadedAndPosted = 0;
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $newUser = $db->query(
            "INSERT INTO users(username, email, password, avatar_id, games_downloaded,games_posted) VALUES(:username, :email, :password, :avatar_id,:games_downloaded, :games_posted)"
        ,[
            "username"=>$username,
            "email"=>$email,
            "password"=>$hashedPassword,
            "avatar_id"=>$avatarId,
            "games_downloaded"=>$default_gamesDownloadedAndPosted,
            "games_posted"=>$default_gamesDownloadedAndPosted
        ]);
        $_SESSION['isLogged'] = true;
        $_SESSION['user'] = $username;

        redirect("/");
    }
}
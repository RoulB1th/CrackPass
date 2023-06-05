<?php declare(strict_types=1);

namespace App\controllers;

use Core\database;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

define("MAX_SIZE", 5621440);

require_once ROOT . '/functions.php';
require_once ROOT . '/validator/validation.php';

class game
{

    public static function index($id) : void 
    {
        $conn = new database;

        $game = $conn->query("SELECT * FROM games WHERE id=:id",[
            "id"=>$id
        ])->fetchAssociative(); 
        #Get Game
        $poster_id = $game["posted_by"];
        $poster = $conn->query("SELECT * FROM users WHERE id=:id",[
            "id"=>$poster_id
        ])->fetchAssociative();
        #Avatar
        $primaryPicture = $conn->query("SELECT * FROM avatar WHERE id=:id",[
            "id"=>$game["primary_picture_id"]
        ])->fetchAssociative()["uri"] ?? false;

        $secondaryPicture = $conn->query("SELECT * FROM avatar WHERE id=:id",[
            "id"=>$game["secondary_picture_id"]
        ])->fetchAssociative()["uri"] ?? false;

        $thirdPicture = $conn->query("SELECT * FROM avatar WHERE id=:id",[
            "id"=>$game["third_picture_id"]
        ])->fetchAssociative()["uri"] ?? false;

        if(!$game){
            http_response_code(404);
            view("404",[
                "game"=>true
            ]);
            return;
        }
        view("game",[
            "game"=>$game,
            "poster"=>$poster,
            "primaryPicture"=>$primaryPicture,
            "secondaryPicture"=>$secondaryPicture,
            "thirdPicture"=>$thirdPicture
        ]);
    }
    public static function game(): void
    {

        $conn = new database;
        if(!isset($_SESSION['isLogged'])){
            redirect("/");
            return;
        }
        $errors = [];
        $attempts = rateLimit();
        if($attempts){
            $errors["rate"] = "Please Try Again Later";
            view("post",[
                "errors"=>$errors
            ]);
            die();
        }
        $user = $_SESSION['user'];
        $user = $conn->query("SELECT * FROM users WHERE username=:username",[
            "username"=>$user
        ])->fetchAssociative();
        $user_id = $user["id"];
        $user_games_posted = $user["games_posted"];
        $rows = $conn->query("SELECT  count(*) as c FROM games WHERE posted_by=:user_id and posted_at = CURDATE()",[
            "user_id"=>$user_id
        ])->fetchAssociative();
        $count = $rows["c"];
        if($count >= 5){
            if($user_id!=1){
                $errors["limit"] = "Your Max Limit Has Been Reached , Try Again Tomorrow";
            }
        }
        
        $request = Request::createFromGlobals();
        $title = $_POST['title'];
        $description = $_POST['description'];
        $drive = $_POST['drive'];
        $mega = $_POST['mega'];
        $anon = $_POST['anon'];
        $torrent = $_POST['torrent'];
        $youtube = $_POST['youtube'];

        if(empty($youtube)){
            $youtube = null;
        }else if(empty($torrent)){
            $torrent = null;
        }else if(empty($anon)){
            $anon = null;
        }else if(empty($mega)){
            $mega = null;
        }else if(empty($drive)){
            $drive = null;
        }

        if(!validateTitle($title)){
            $errors["title"] = "A Title Must Contain 2-40 Characters";
        }
        if(!validateDescription($description)){
            $errors["desc"] = "The Description Must Contain 200-1000 Characters";
        }
        $primaryPicture = null;
        $secondPicture = null;
        $thirdPicture = null;
        $uri = null;

        $avatar_id = null;

        if(empty($_FILES['image']['tmp_name']) || !is_uploaded_file($_FILES['image']['tmp_name'][0]))
        {
            $uri = "images/Icon.png";
            $avatar_id = $conn->query("SELECT * FROM avatar WHERE uri=:uri",[
                "uri"=>$uri
            ])->fetchAssociative()["id"] ?? null;
            if($avatar_id == null){
                $conn->query("INSERT INTO avatar(uri, is_games) VALUES(:uri, 1)",[
                    "uri"=>$uri
                ]);
                $avatar_id = $conn->query("SELECT * FROM avatar WHERE uri=:uri",[
                    "uri"=>$uri
                ])->fetchAssociative()["id"];
            }
        }else{
            $files = $_FILES['image'];
            $names = $files["name"];
            $ext = $files["type"];
            $sizes = $files['size'];

            if(count($names) > 3){
                $errors["count"] = "Only 3 screenshots allowed";
            }
            

            foreach($ext as $extension){
                if(!str_contains($extension, "image")){
                    $errors["type"] = "Provided File Must Be An Image";
                }
            }
            foreach($sizes as $size){
                if($size > MAX_SIZE){
                    $errors["size"] = "Provided File Must Be Smaller Than 5MB";
                }
            }
            if(!empty($errors)){
                view("post",[
                    "errors"=>$errors
                ]);
                return;
            }

            if(count($names) == 1){
                $primaryExtension = pathinfo($names[0],PATHINFO_EXTENSION);
                $names[0] = "primary." . $primaryExtension;
            }else if(count($names) == 2){
                $primaryExtension = pathinfo($names[0],PATHINFO_EXTENSION);
                $secondaryExtension = pathinfo($names[1],PATHINFO_EXTENSION);
                $names[0] = "primary." . $primaryExtension;
                $names[1] = "secondary." . $secondaryExtension;
            }else if(count($names) == 3){
                $primaryExtension = pathinfo($names[0],PATHINFO_EXTENSION);
                $secondaryExtension = pathinfo($names[1],PATHINFO_EXTENSION);
                $thirdExtension = pathinfo($names[2],PATHINFO_EXTENSION);
                $names[0] = "primary." . $primaryExtension;
                $names[1] = "secondary." . $secondaryExtension;
                $names[2] = "third." . $thirdExtension;
            }
            $primaryPicture = $names[0] ?? null;
            $secondPicture = $names[1] ?? null;
            $thirdPicture = $names[2] ?? null;

            $tmp_names = $files["tmp_name"];
            $files_array = array_combine($tmp_names, $names);
            $path = 'images/'.date("dmyhms") . '/' . $files["size"][0] * rand(1,50) . '/';
            mkdir($path,0777,true);
            foreach($files_array as $tmp_name=>$file_name){
                if(move_uploaded_file($tmp_name, $path.$file_name)){
                    $conn->query("INSERT INTO avatar(uri,is_games) VALUES(:uri, :is_games)",[
                        "uri"=>$path.$file_name,
                        "is_games"=>1
                    ]);
                    $avatar_id = $conn->query("SELECT * FROM avatar WHERE uri=:uri",[
                        "uri"=>$path.$file_name
                    ])->fetchAssociative()["id"];
                    
                }else{
                    $errors["file_error"] = "An Error Occured While Uploading Image , Please Report This to us";
                }
            }
        }
        if(!empty($errors)){
            view("post",[
                "errors"=>$errors
            ]);
            return;
        }


        $now = date("y-m-d");
        $primaryQuery = $path.$primaryPicture;
        $secondaryQuery = $path.$secondPicture ?? null;
        $thirdQuery = $path.$thirdPicture ?? null;
        
        $primaryId = $conn->query("SELECT * FROM avatar WHERE uri=:uri",[
            "uri"=>$primaryQuery
        ])->fetchAssociative()["id"] ?? $avatar_id;
        $secondaryId = $conn->query("SELECT * FROM avatar WHERE uri=:uri",[
            "uri"=>$secondaryQuery
        ])->fetchAssociative()["id"] ?? null;
        $thirdId = $conn->query("SELECT * FROM avatar WHERE uri=:uri",[
            "uri"=>$thirdQuery
        ])->fetchAssociative()["id"] ?? null;

        

        $conn->query("INSERT INTO games(is_verified,name,posted_at,posted_by,visits,avatar_id,anonfiles,meganz,comments_id,description,torrent,drive,primary_picture_id,secondary_picture_id,third_picture_id,youtube_link)
        VALUES(0,:name,:posted_at,:posted_by,:visits,:avatar_id,:anonfiles,:meganz,:comments_id,:description,:torrent,:drive,:primary_picture_id,:secondary_picture_id,:third_picture_id,:youtube_link)
        ",[
            "name"=>$title,
            "posted_at"=>$now,
            "posted_by"=>$user_id,
            "visits"=>0,
            "avatar_id"=>$avatar_id,
            "anonfiles"=>$anon,
            "meganz"=>$mega,
            "comments_id"=>null,
            "description"=>$description,
            "torrent"=>$torrent,
            "drive"=>$drive,
            "primary_picture_id"=>$primaryId,
            "secondary_picture_id"=>$secondaryId,
            "third_picture_id"=>$thirdId,
            "youtube_link"=>$youtube,
        ]);
        $conn->query("UPDATE users SET games_posted = games_posted + 1 WHERE id=:id",[
            "id"=>$user_id
        ]);
        redirect("/");

    }
}
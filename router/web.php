<?php declare(strict_types=1);

use Core\router;
use App\controllers\home;
use App\controllers\register;
use App\controllers\logout;
use App\controllers\users;
use App\controllers\login;
use App\controllers\game;

router::GET('/', [home::class, "index"]);

router::GET('/register', [home::class, "register"]);

router::GET('/test', [home::class, "test"]);

router::GET('/logout',[logout::class, "index"]);

router::GET('/login',[home::class, "login"]);

router::GET('/users',[users::class, "index"]);

router::GET('/game/new',[home::class, "post"]);

router::GET('/game/{id}',[game::class, "index"]);

router::POST('/register/new',[register::class, "register"]);

router::POST('/login/new',[login::class, "login"]);

router::POST('/game/post',[game::class, "game"]);

router::run();
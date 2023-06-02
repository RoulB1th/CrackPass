<?php declare(strict_types=1);

use Core\router;
use App\controllers\home;
use App\controllers\register;
use App\controllers\logout;
use App\controllers\users;

router::GET('/', [home::class, "index"]);

router::GET('/register', [home::class, "register"]);

router::GET('/test', [home::class, "test"]);

router::GET('/logout',[logout::class, "index"]);

router::GET('/users',[users::class, "index"]);

router::POST('/register/new',[register::class, "register"]);

router::run();
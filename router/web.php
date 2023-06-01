<?php declare(strict_types=1);

use Core\router;
use App\controllers\home;
use App\controllers\register;

router::GET('/', [home::class, "index"]);

router::GET('/register', [home::class, "register"]);

router::POST('/register/new',[register::class, "register"]);

router::run();
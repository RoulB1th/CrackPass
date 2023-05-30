<?php declare(strict_types=1);

use Core\router;
use App\controllers\home;

router::GET('/', [home::class, "index"]);

router::GET('/register', [home::class, "register"]);

router::run();
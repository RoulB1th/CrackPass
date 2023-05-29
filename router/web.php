<?php declare(strict_types=1);

use Core\router;
use App\controllers\home;

require_once ROOT . "lol.php";

router::GET('/sus', [home::class, "index"]);

router::GET('/', [home::class, "index"]);

router::run();
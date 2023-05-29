<?php declare(strict_types=1);

use Core\router;
use App\controllers\home;

router::GET('/', [home::class, "index"]);

router::run();
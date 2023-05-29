<?php declare(strict_types=1);

use Core\twig;

function view(string $file, array $args = []): void
{
    $twig = new twig;
    $twig::render($file, $args);
}
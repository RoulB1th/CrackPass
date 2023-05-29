<?php declare(strict_types=1);

namespace Core;

require_once ROOT . '/vendor/autoload.php';
use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;

class twig
{
    public static Environment $twig;
    public function __construct()
    {
        $loader = new FilesystemLoader(ROOT . '/views');
        static::$twig = new Environment($loader);
    }
    public static function render(string $view, array $args = []): void
    {
        echo static::$twig->render("{$view}.twig", $args);
    }
}
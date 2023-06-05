<?php declare(strict_types=1);

use Core\twig;
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(ROOT);
$dotenv->load();

function view(string $file, array $args = []): void
{
    $twig = new twig;
    $twig::render($file, $args);
}
function redirect(string $uri){
    header("location:$uri");

}
function rateLimit()
{

    $redis = new Redis();
    $redis->connect($_ENV['REDIS_HOST'], intval($_ENV['REDIS_PORT']));
        $redis->auth('password');
        $max_calls_limit  = 3;
        $time_period      = 30;
        $total_user_calls = 0;

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $user_ip_address = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $user_ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $user_ip_address = $_SERVER['REMOTE_ADDR'];
        }
        if (!$redis->exists($user_ip_address)) {
            $redis->set($user_ip_address, 1);
            $redis->expire($user_ip_address, $time_period);
            $total_user_calls = 1;
        } else {
            $redis->INCR($user_ip_address);
            $total_user_calls = $redis->get($user_ip_address);
            if ($total_user_calls > $max_calls_limit) {
                return true;
            }
        }
        
}
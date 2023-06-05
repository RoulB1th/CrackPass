<?php declare(strict_types=1);

namespace Core;

use Dotenv\Dotenv;
use Doctrine\DBAL\DriverManager;

require_once ROOT . '/functions.php';
require_once ROOT . '/vendor/autoload.php';

class database{
    protected static Dotenv $dotenv;
    public static $conn;
    public static string $query;

    public function __construct()
    {
        static::$dotenv = Dotenv::createImmutable(ROOT);
        static::$dotenv->load();
        $connectionParams = [
            'dbname' => $_ENV['DB_NAME'],
            'user' => $_ENV['DB_USER'],
            'password' => $_ENV['DB_PASSWORD'],
            'host' => $_ENV['DB_HOST'],
            'port'=> $_ENV['DB_PORT'],
            'driver' => 'mysqli',
        ];
        static::$conn = DriverManager::getConnection($connectionParams);
    }
    public static function query(string $query, array $args = [])
    {
        static::$query = $query;
        $results = static::$conn->executeQuery(static::$query,$args);
        return $results;
    }
    public static function build()
    {
        $builder = static::$conn->createQueryBuilder();
        return $builder;
    }
}
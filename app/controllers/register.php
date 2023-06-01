<?php declare(strict_types=1);

namespace App\controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

require_once ROOT . '/functions.php';

class register
{
    public static function register(): void
    {
        $response = new Response();
        $request = Request::createFromGlobals();

        $username = $request->getPayload()->getString("username");
        $password = $request->getPayload()->getString("password");
        $email = $request->getPayload()->getString("email");
    }
}
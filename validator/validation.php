<?php 

use Respect\Validation\Rules;
use Respect\Validation\Validator as v;

function validateUsername(string $username) : bool
{

    $usernameValidator = new Rules\AllOf(
        new Rules\Alnum(),
        new Rules\Length(3, 20),
        new Rules\NoWhitespace(),
    );

    return $usernameValidator->validate($username);

}

function validatePassword(string $password) : bool
{
    if(strlen($password) < 8){
        return false;
    }
    return true;
}

function validateEmail(string $email) : bool 
{
    return v::email()->validate($email); 
}
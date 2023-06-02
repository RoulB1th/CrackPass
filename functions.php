<?php declare(strict_types=1);

use Core\twig;

function view(string $file, array $args = []): void
{
    $twig = new twig;
    $twig::render($file, $args);
}
function redirect(string $uri){
    header("location:$uri");
}
function uploadFile($file) {
    $path = "/images/public/images/" . date("dmy hms") . $file['size'] . $file['name'];
    $location = mkdir($path);

    if(move_uploaded_file($file['tmp_name'], '/public/images'))
    {
        return true;
    }else{
        echo "error";
    }

}
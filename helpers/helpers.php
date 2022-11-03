<?php

use RedBeanPHP\R;

function render($template, $data = array())
{
    $loader = new \Twig\Loader\FilesystemLoader("../views");
    $twig = new \Twig\Environment($loader);

    if (isset($_SESSION['user_id'])) {
        $data['user'] = R::findOne('user', 'id = ? ', [$_SESSION['user_id']]);
    }

    echo $twig->render($template, $data);
}


function error($errorNumber, $errorMessage)
{
    http_response_code(404);

    render('error.twig', array(
        'errorMessage' => $errorMessage,
        'errorNumber' => $errorNumber
    ));
}

<?php
require 'vendor/autoload.php';

$app = new \Slim\Slim(array(
    'debug' => true
));

$app->get('/users', function () use ($app) {
    $users = array('toto');
    $app->response()->header('Content-Type', 'application/json');

    echo json_encode($users);
});

//$app->get('/users/:id', 'totoMaFonction');

$app->run();
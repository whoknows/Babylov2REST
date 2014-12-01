<?php

require 'vendor/autoload.php';

$app = new \Slim\Slim(array(
    'debug' => true
));

$app->get('/users', function () use ($app) {
    $users = \Models\User::getFullList("WHERE enabled = 1");
    $app->response()->header('Content-Type', 'application/json');

    echo json_encode(array_values($users));
});

//$app->get('/users/:id', 'totoMaFonction');

$app->run();
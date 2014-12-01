<?php

require 'vendor/autoload.php';

$app = new \Slim\Slim(array(
    'debug' => true
));

$app->get('/users', function () use ($app) {
    echo json_encode(array_values(\Models\User::getFullList("WHERE enabled = 1")));
});

$app->get('/users/:id', function ($id) use ($app) {
    echo json_encode(array_values(\Models\User::getFullList("WHERE a.id = $id")));
});

//$app->get('/users/:id', 'totoMaFonction');
$app->response()->header('Content-Type', 'application/json');
$app->run();
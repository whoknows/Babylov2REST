<?php

session_cache_limiter(false);
session_start();

require 'vendor/autoload.php';

use \Models\User;
use \Models\Game;
use \Models\Slot;

$app = new \Slim\Slim(array('debug' => true));

/**
 * Utilisateurs
 */
$app->get('/isconnected', function () {
    echo json_encode(User::isUserConnected());
});

$app->get('/logout', function () {
    echo json_encode(User::doLogoutAction());
});

$app->get('/login/:login/:password', function ($login, $password) {
    echo json_encode(User::doLoginAction($login, $password));
});

$app->get('/users', function () {
    echo json_encode(array_values(User::getFullList()));
});

$app->get('/users/:id', function ($id) {
    echo json_encode(User::getUniqueUser(User::getFullList("WHERE a.id = $id")));
});

/**
 * Parties
 */
$app->get('/games/:limit', function($limit){
    echo json_encode(array_values(Game::getAllGames("", "LIMIT 0," . ($limit * 4))));
});

$app->post('/games', function($limit) use($app) {
    //$app->request->post('');
    echo json_encode(Game::post($data));
});

/**
 * Planification
 */
$app->get('/slot', function () {
    echo json_encode(array_values(Slot::getAllSlots()));
});

$app->post('/slot/:creneau/:user_id', function ($creneau, $user_id) {
    echo json_encode(Slot::post($creneau, $user_id));
});

$app->delete('/slot/:creneau/:user_id', function ($creneau, $user_id) {
    echo json_encode(Slot::delete($creneau, $user_id));
});

$app->response()->header('Content-Type', 'application/json');
$app->run();

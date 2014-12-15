<?php

session_cache_limiter(false);
session_start();

require 'vendor/autoload.php';

use \Models\User;
use \Models\Game;
use \Models\Slot;
use \Models\HomeData;

$app = new \Slim\Slim(array('debug' => true));

/**
 * Authentification
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

/**
 * Utilisateurs
 */
$app->get('/users', function () {
    echo json_encode(array_values(User::getFullList()));
});

$app->get('/users/:id', function ($id) {
    echo json_encode(User::getUniqueUser(User::getFullList("WHERE a.id = $id")));
});

$app->get('/usergraph/:id', function ($id) {
    echo json_encode(array(
        'userdata' => Game::getUsersGameData("WHERE a.id = $id", "date", false),
        'total' => Game::getTotalGames("yearmonth")
    ));
});

$app->get('/userdetail/:id', function ($id) {
    echo json_encode(User::getUserDetail($id));
});

$app->post('/users', function() use($app){
    echo json_encode(User::post($app->request()->post()));
});

$app->put('/users', function() use($app){
    echo json_encode(User::put($app->request()->post()));
});

$app->delete('/users', function() use($app){
    echo json_encode(User::delete($app->request()->post()));
});

/**
 * Parties
 */
$app->get('/games', function() use($app) {
    $date = $app->request()->get('date');
    $limit = $app->request()->get('limit');

    if($date !== null || $date != ''){
        $where = "WHERE date = '$date'";
    } else {
        $where = "";
    }

    if($limit !== null || $limit != ''){
        $limit = "LIMIT 0, " . $limit * 4;
    } else {
        $limit = "";
    }

    echo json_encode(array_values(Game::getAllGames($where, $limit)));
});

$app->post('/games', function() use($app) {
    echo json_encode(array('id' => Game::post($app->request->post())));
});

$app->delete('/games/:id', function($id) {
    echo json_encode(Game::delete($id));
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

/**
 * Home data
 */
$app->get('/homedata', function () {
    echo json_encode(HomeData::get());
});

$app->response()->header('Content-Type', 'application/json');
$app->run();

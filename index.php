<?php

$loader = new \Phalcon\Loader();
$loader->registerDirs(array(__DIR__ . '/Models/'))->register();

$di = new \Phalcon\DI\FactoryDefault();
$di->set('db', function(){
    return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
        "host" => "localhost",
        "username" => "root",
        "password" => "secret",
        "dbname" => "babyv2"
    ));
});

$app = new \Phalcon\Mvc\Micro($di);

/*
 * Routes definitions
 */

$app->get('/api/user', function() use ($app) {
    $users = User::getFullList();

    //echo json_encode($users);
});

$app->get('/api/user/search/{name}', function($name) use ($app) {
    // Finds a user by name
});

$app->get('/api/user/{id:[0-9]+}', function($id) use ($app) {
    // Finds a user by id
});

$app->post('/api/user', function() use ($app) {
    // Adds a new user
});

$app->put('/api/user/{id:[0-9]+}', function() use ($app) {
    // Updates user based on primary key
});

$app->delete('/api/game/{id:[0-9]+}', function() use ($app) {
    // Deletes game based on primary key
});

$app->handle();

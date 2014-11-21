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

//Create and bind the DI to the application
$app = new \Phalcon\Mvc\Micro($di);

$app->get('/api/user', function() use ($app) {
    $user = User::findFirst(2);

    $data = array();

    foreach ($user->games as $userGame) {
        $data[] = $userGame;
    }

    echo json_encode($data);
});

$app->get('/api/user/search/{name}', function($name) use ($app) {
    // Finds a user by name
});

$app->get('/api/user/{id:[0-9]+}', function($id) use ($app) {
    // Finds a user by ud
});

$app->post('/api/user', function() use ($app) {
    // Adds a new user
});

$app->put('/api/user/{id:[0-9]+}', function() use ($app) {
    // Updates user based on primary key
});

$app->delete('/api/user/{id:[0-9]+}', function() use ($app) {
    // Deletes user based on primary key
});

$app->handle();

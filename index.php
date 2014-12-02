<?php

require 'vendor/autoload.php';

$app = new \Slim\Slim(array(
    'debug' => true
));

$app->get('/users', function () {
    echo json_encode(array_values(\Models\User::getFullList("WHERE enabled = 1")));
});

$app->get('/users/:id', function ($id) {
    $user = \Models\User::getFullList("WHERE a.id = $id");

    if (sizeof($user) == 1) {
        $user = $user[$id];
    }

    echo json_encode($user);
});

/**
 * Planification
 */
$app->get('/slot', function () {
    echo json_encode(array_values(\Models\Slot::getAllSlots()));
});

$app->put('/slot/:creneau/:user_id', function ($creneau, $user_id) {
    echo json_encode(\Models\Slot::put($creneau, $user_id));
});

$app->delete('/slot/:creneau/:user_id', function ($creneau, $user_id) {
    echo json_encode(\Models\Slot::delete($creneau, $user_id));
});

$app->response()->header('Content-Type', 'application/json');
$app->run();
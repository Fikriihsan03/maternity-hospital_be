<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});
$router->post('api/v1/child-birth','ChildBirthController@store');
$router->put('api/v1/child-birth/{id}','ChildBirthController@update');
$router->delete('api/v1/child-birth/{id}','ChildBirthController@destroy');

